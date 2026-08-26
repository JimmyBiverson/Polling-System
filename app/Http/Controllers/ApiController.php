<?php

namespace App\Http\Controllers;

use App\Models\PollingStation;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function wardsByConstituency($constituencyId)
    {
        $wards = \App\Models\Ward::where('constituency_id', $constituencyId)
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
        $candidates = \App\Models\Candidate::where('election_type_id', $electionTypeId)
            ->orderBy('name')
            ->get(['id', 'name', 'party']);

        return response()->json($candidates);
    }

    public function liveStats()
    {
        $total = \App\Models\VoteSubmission::count();
        $verified = \App\Models\VoteSubmission::where('status', 'verified')->count();
        $pending = \App\Models\VoteSubmission::where('status', 'pending')->count();

        return response()->json([
            'total' => $total,
            'verified' => $verified,
            'pending' => $pending,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
