<?php

namespace App\Http\Controllers;

use App\Models\Constituency;
use App\Models\PollingStation;
use App\Models\VoteSubmission;
use App\Models\Ward;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $constituencies = Constituency::with('wards')->get();

        return view('reports.index', compact('constituencies'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:constituency,ward,station',
            'identifier' => 'required|string',
        ]);

        $type = $request->type;
        $identifier = $request->identifier;

        $report = match ($type) {
            'constituency' => $this->buildConstituencyReport($identifier),
            'ward' => $this->buildWardReport($identifier),
            'station' => $this->buildStationReport($identifier),
        };

        if (str_starts_with($report, 'Error:')) {
            return back()->with('error', $report);
        }

        return view('reports.generate', compact('report', 'type'));
    }

    private function buildConstituencyReport(string $identifier): string
    {
        $constituency = Constituency::where('name', 'like', "%{$identifier}%")
            ->orWhere('id', $identifier)
            ->first();

        if (! $constituency) {
            return 'Error: Constituency not found.';
        }

        $wards = Ward::where('constituency_id', $constituency->id)->get();
        $lines = [];
        $lines[] = '╔══════════════════════════════════════════════╗';
        $lines[] = '║  KENYA PRESIDENTIAL ELECTION 2027';
        $lines[] = '║  CONSTITUENCY TALLY REPORT';
        $lines[] = '╚══════════════════════════════════════════════╝';
        $lines[] = '';
        $lines[] = 'Constituency: '.$constituency->name;
        $lines[] = 'Generated: '.now()->format('d M Y H:i:s');
        $lines[] = '';

        foreach ($wards as $ward) {
            $lines[] = '─── Ward: '.$ward->name.' ───';
            $stations = PollingStation::where('ward_id', $ward->id)->get();

            foreach ($stations as $station) {
                $latest = $station->submissions()->latest()->first();
                if ($latest) {
                    $details = $latest->details()->with('candidate')->get();
                    $lines[] = '  Station: '.$station->name;
                    $lines[] = '  Agent: '.($latest->agent_name ?? 'N/A');
                    foreach ($details as $d) {
                        $lines[] = '    '.($d->candidate->name ?? 'Unknown').': '.$d->votes;
                    }
                    $lines[] = '    Spoilt: '.$latest->spoilt_votes.' | Cast: '.$latest->total_votes_cast.' | Registered: '.$latest->registered_voters;
                    $turnout = $latest->registered_voters > 0 ? round(($latest->total_votes_cast / $latest->registered_voters) * 100, 1) : 0;
                    $lines[] = '    Turnout: '.$turnout.'%';
                } else {
                    $lines[] = '  Station: '.$station->name.' (No data)';
                }
                $lines[] = '';
            }
        }

        $lines[] = '═══════════════════════════════════════════════';

        return implode("\n", $lines);
    }

    private function buildWardReport(string $identifier): string
    {
        $ward = Ward::where('name', 'like', "%{$identifier}%")
            ->orWhere('id', $identifier)
            ->first();

        if (! $ward) {
            return 'Error: Ward not found.';
        }

        $stations = PollingStation::where('ward_id', $ward->id)->get();
        $lines = [];
        $lines[] = '╔══════════════════════════════════════════════╗';
        $lines[] = '║  WARD TALLY REPORT: '.strtoupper($ward->name);
        $lines[] = '╚══════════════════════════════════════════════╝';
        $lines[] = '';

        foreach ($stations as $station) {
            $latest = $station->submissions()->latest()->first();
            $lines[] = 'Station: '.$station->name;
            if ($latest) {
                $details = $latest->details()->with('candidate')->get();
                foreach ($details as $d) {
                    $lines[] = '  '.($d->candidate->name ?? 'Unknown').': '.$d->votes;
                }
                $lines[] = '  Spoilt: '.$latest->spoilt_votes.' | Cast: '.$latest->total_votes_cast.' | Registered: '.$latest->registered_voters;
                $turnout = $latest->registered_voters > 0 ? round(($latest->total_votes_cast / $latest->registered_voters) * 100, 1) : 0;
                $lines[] = '  Turnout: '.$turnout.'%';
                $lines[] = '  Updated: '.$latest->submitted_at->format('d M Y H:i');
            } else {
                $lines[] = '  No votes recorded.';
            }
            $lines[] = '';
        }

        $lines[] = '═══════════════════════════════════════════════';

        return implode("\n", $lines);
    }

    private function buildStationReport(string $identifier): string
    {
        $station = PollingStation::with('ward.constituency.county')
            ->where('name', 'like', "%{$identifier}%")
            ->orWhere('id', $identifier)
            ->first();

        if (! $station) {
            return 'Error: Polling Station not found.';
        }

        $lines = [];
        $lines[] = '╔══════════════════════════════════════════════╗';
        $lines[] = '║  POLLING STATION REPORT';
        $lines[] = '╚══════════════════════════════════════════════╝';
        $lines[] = '';
        $lines[] = 'Station: '.$station->name;
        $lines[] = 'Ward: '.($station->ward->name ?? 'N/A');
        $lines[] = 'Constituency: '.($station->ward?->constituency?->name ?? 'N/A');
        $lines[] = 'County: '.($station->county?->name ?? 'N/A');

        $latest = $station->submissions()->latest()->first();
        if ($latest) {
            $lines[] = '';
            $lines[] = 'Agent: '.$latest->agent_name;
            $lines[] = 'Agent Code: '.$latest->agent_code;
            $lines[] = 'Presiding Officer: '.($latest->presiding_officer ?? 'N/A');
            $lines[] = '';
            $lines[] = 'Vote Breakdown:';
            $details = $latest->details()->with('candidate')->get();
            foreach ($details as $d) {
                $lines[] = '  '.str_pad($d->candidate->name ?? 'Unknown', 20).' '.str_pad($d->votes, 8);
            }
            $lines[] = '';
            $lines[] = 'Spoilt Votes:     '.$latest->spoilt_votes;
            $lines[] = 'Total Cast:       '.$latest->total_votes_cast;
            $lines[] = 'Registered:       '.$latest->registered_voters;
            $turnout = $latest->registered_voters > 0 ? round(($latest->total_votes_cast / $latest->registered_voters) * 100, 1) : 0;
            $lines[] = 'Turnout:          '.$turnout.'%';
            $lines[] = 'Status:           '.strtoupper($latest->status);
            $lines[] = 'Submitted:        '.$latest->submitted_at->format('d M Y H:i:s');
            $lines[] = 'Hash:             '.substr($latest->submission_hash, 0, 16).'...';
        } else {
            $lines[] = '';
            $lines[] = 'No votes recorded.';
        }

        $lines[] = '';
        $lines[] = '═══════════════════════════════════════════════';

        return implode("\n", $lines);
    }

    public function exportCsv()
    {
        $submissions = VoteSubmission::with(['pollingStation.ward.constituency.county', 'electionType', 'details.candidate'])
            ->where('status', 'verified')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment;filename=kakamega_results_'.now()->format('Y-m-d').'.csv',
        ];

        $callback = function () use ($submissions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['County', 'Constituency', 'Ward', 'Station', 'Election', 'Candidate', 'Votes', 'Spoilt', 'Total Cast', 'Registered', 'Status', 'Timestamp']);

            foreach ($submissions as $s) {
                foreach ($s->details as $detail) {
                    fputcsv($file, [
                        $s->pollingStation?->county?->name,
                        $s->pollingStation?->constituency()?->name ?? $s->pollingStation?->ward?->constituency?->name,
                        $s->pollingStation?->ward?->name,
                        $s->pollingStation?->name,
                        $s->electionType?->name,
                        $detail->candidate?->name,
                        $detail->votes,
                        $s->spoilt_votes,
                        $s->total_votes_cast,
                        $s->registered_voters,
                        $s->status,
                        $s->submitted_at,
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
