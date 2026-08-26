<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Candidate;
use App\Models\ElectionType;
use App\Models\PollingStation;
use App\Models\VoteDetail;
use App\Models\VoteSubmission;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoteSubmissionController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        $electionTypes = ElectionType::where('is_active', true)->get();
        $stations = PollingStation::with('ward')->get();

        return view('votes.create', compact('electionTypes', 'stations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'election_type_id' => 'required|exists:election_types,id',
            'polling_station_id' => 'required|exists:polling_stations,id',
            'agent_name' => 'required|string|max:255',
            'agent_code' => 'required|string|max:50',
            'presiding_officer' => 'nullable|string|max:255',
            'candidate_votes' => 'required|array|min:1',
            'candidate_votes.*.candidate_id' => 'required|exists:candidates,id',
            'candidate_votes.*.votes' => 'required|integer|min:0',
            'spoilt_votes' => 'required|integer|min:0',
            'total_votes_cast' => 'required|integer|min:0',
            'registered_voters' => 'required|integer|min:0',
        ]);

        $candidateSum = collect($request->candidate_votes)->sum('votes');
        $expectedTotal = $candidateSum + $request->spoilt_votes;

        if ($expectedTotal !== (int) $request->total_votes_cast) {
            return back()->withErrors([
                'total_votes_cast' => "Vote mismatch: candidates ({$candidateSum}) + spoilt ({$request->spoilt_votes}) = {$expectedTotal}, but total cast is {$request->total_votes_cast}.",
            ])->withInput();
        }

        DB::beginTransaction();

        try {
            $submission = VoteSubmission::create([
                'polling_station_id' => $request->polling_station_id,
                'election_type_id' => $request->election_type_id,
                'user_id' => Auth::id(),
                'agent_name' => $request->agent_name,
                'agent_code' => $request->agent_code,
                'presiding_officer' => $request->presiding_officer,
                'spoilt_votes' => $request->spoilt_votes,
                'total_votes_cast' => $request->total_votes_cast,
                'registered_voters' => $request->registered_voters,
                'ip_address' => $request->ip(),
                'device_info' => $request->userAgent(),
                'submitted_at' => now(),
            ]);

            foreach ($request->candidate_votes as $cv) {
                VoteDetail::create([
                    'vote_submission_id' => $submission->id,
                    'candidate_id' => $cv['candidate_id'],
                    'votes' => $cv['votes'],
                ]);
            }

            $submission->submission_hash = $submission->generateHash();
            $submission->save();

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'vote_submitted',
                'model_type' => VoteSubmission::class,
                'model_id' => $submission->id,
                'new_values' => $request->except(['candidate_votes']),
                'ip_address' => $request->ip(),
                'description' => "Vote submission for polling station #{$request->polling_station_id}",
            ]);

            DB::commit();

            return redirect()->route('dashboard')
                ->with('success', 'Report submitted successfully! Submission #' . $submission->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Submission failed: ' . $e->getMessage());
        }
    }

    public function show(VoteSubmission $submission)
    {
        $submission->load(['pollingStation.ward.constituency.county', 'electionType', 'details.candidate', 'user']);

        return view('votes.show', compact('submission'));
    }

    public function verify(Request $request, VoteSubmission $submission)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'notes' => 'nullable|string',
        ]);

        $submission->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'submission_' . $request->status,
            'model_type' => VoteSubmission::class,
            'model_id' => $submission->id,
            'new_values' => ['status' => $request->status, 'notes' => $request->notes],
            'ip_address' => $request->ip(),
            'description' => "Submission #{$submission->id} {$request->status}",
        ]);

        return back()->with('success', "Submission #{$submission->id} {$request->status}.");
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'election_type_id' => 'required|exists:election_types,id',
            'bulk_data' => 'required|string',
        ]);

        $lines = array_filter(explode("\n", $request->bulk_data), fn($l) => trim($l) !== '');
        $results = ['success' => 0, 'errors' => 0, 'details' => []];

        DB::beginTransaction();

        try {
            foreach ($lines as $idx => $line) {
                $result = $this->processBulkLine(trim($line), $request->election_type_id, $idx + 1);
                $results['details'][] = $result;
                if ($result['success']) {
                    $results['success']++;
                } else {
                    $results['errors']++;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Bulk processing failed: ' . $e->getMessage());
        }

        return back()->with('bulk_results', $results);
    }

    private function processBulkLine(string $line, int $electionTypeId, int $lineNum): array
    {
        $fields = array_map('trim', explode(',', $line));

        if (count($fields) < 9) {
            return ['success' => false, 'line' => $lineNum, 'message' => 'Insufficient fields (min 9 required).'];
        }

        $constName = $fields[0];
        $wardName = $fields[1];
        $stationName = $fields[2];
        $agentName = $fields[3];
        $agentCode = $fields[4] ?? '';
        $presidingOfficer = $fields[5] ?? '';

        $spoilt = (int) ($fields[count($fields) - 3] ?? 0);
        $totalCast = (int) ($fields[count($fields) - 2] ?? 0);
        $registered = (int) ($fields[count($fields) - 1] ?? 0);

        $candStart = 6;
        $candEnd = count($fields) - 3;

        if ($candEnd <= $candStart) {
            return ['success' => false, 'line' => $lineNum, 'message' => 'No candidate pairs found.'];
        }

        $candidates = [];
        for ($i = $candStart; $i < $candEnd; $i++) {
            $pair = explode(':', $fields[$i]);
            if (count($pair) !== 2) continue;
            $candidates[] = ['name' => trim($pair[0]), 'votes' => (int) trim($pair[1])];
        }

        if (empty($candidates)) {
            return ['success' => false, 'line' => $lineNum, 'message' => 'No valid candidate:votes pairs.'];
        }

        $ward = Ward::where('name', $wardName)->first();
        if (!$ward) {
            return ['success' => false, 'line' => $lineNum, 'message' => "Ward '{$wardName}' not found."];
        }

        $station = PollingStation::where('name', $stationName)->where('ward_id', $ward->id)->first();
        $isNew = false;

        if (!$station) {
            $station = PollingStation::create([
                'ward_id' => $ward->id,
                'name' => $stationName,
                'registered_voters' => $registered,
            ]);
            $isNew = true;
        }

        $submission = VoteSubmission::create([
            'polling_station_id' => $station->id,
            'election_type_id' => $electionTypeId,
            'user_id' => Auth::id(),
            'agent_name' => $agentName,
            'agent_code' => $agentCode,
            'presiding_officer' => $presidingOfficer,
            'spoilt_votes' => $spoilt,
            'total_votes_cast' => $totalCast,
            'registered_voters' => $registered,
            'ip_address' => request()->ip(),
            'submitted_at' => now(),
        ]);

        foreach ($candidates as $cand) {
            $candidate = Candidate::firstOrCreate(
                ['name' => $cand['name'], 'election_type_id' => $electionTypeId],
                ['party' => null]
            );
            VoteDetail::create([
                'vote_submission_id' => $submission->id,
                'candidate_id' => $candidate->id,
                'votes' => $cand['votes'],
            ]);
        }

        $submission->submission_hash = $submission->generateHash();
        $submission->save();

        return [
            'success' => true,
            'line' => $lineNum,
            'message' => $isNew ? "New station created & votes saved" : "Votes updated",
            'station' => $stationName,
        ];
    }
}
