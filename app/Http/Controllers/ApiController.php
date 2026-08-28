<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\PollingStation;
use App\Models\VoteSubmission;
use App\Models\Ward;

class ApiController extends Controller
{
    public function wardsByConstituency($constituencyId)
    {
        $wards = Ward::where('constituency_id', $constituencyId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($wards);
    }

    public function stationsByWard($wardId)
    {
        $stations = PollingStation::where('ward_id', $wardId)
            ->orderBy('name')
            ->get(['id', 'name', 'registered_voters']);

        return response()->json($stations);
    }

    public function candidatesByElection($electionTypeId)
    {
        $candidates = Candidate::where('election_type_id', $electionTypeId)
            ->orderBy('name')
            ->get(['id', 'name', 'party']);

        return response()->json($candidates);
    }

    public function liveStats()
    {
        $total = VoteSubmission::count();
        $verified = VoteSubmission::where('status', 'verified')->count();
        $pending = VoteSubmission::where('status', 'pending')->count();

        return response()->json([
            'total' => $total,
            'verified' => $verified,
            'pending' => $pending,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
