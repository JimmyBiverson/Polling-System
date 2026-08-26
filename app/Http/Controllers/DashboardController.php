<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Candidate;
use App\Models\Constituency;
use App\Models\ElectionType;
use App\Models\PollingStation;
use App\Models\VoteSubmission;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->agentDashboard();
    }

    private function adminDashboard()
    {
        $totalSubmissions = VoteSubmission::count();
        $verifiedSubmissions = VoteSubmission::where('status', 'verified')->count();
        $pendingSubmissions = VoteSubmission::where('status', 'pending')->count();
        $totalStations = PollingStation::count();
        $stationsReported = VoteSubmission::select('polling_station_id')
            ->distinct()
            ->count('polling_station_id');

        $totalVotes = VoteSubmission::where('status', 'verified')
            ->sum('total_votes_cast');
        $totalSpoilt = VoteSubmission::where('status', 'verified')
            ->sum('spoilt_votes');
        $totalRegistered = VoteSubmission::where('status', 'verified')
            ->sum('registered_voters');
        $turnout = $totalRegistered > 0 ? round(($totalVotes / $totalRegistered) * 100, 1) : 0;

        $latestSubmission = VoteSubmission::with(['pollingStation.ward.constituency'])
            ->latest('submitted_at')
            ->first();

        $constituencies = Constituency::with(['wards.pollingStations.submissions' => function ($q) {
            $q->latest('submitted_at')->limit(1);
        }])->get();

        $electionTypes = ElectionType::where('is_active', true)->get();

        $recentSubmissions = VoteSubmission::with(['pollingStation.ward', 'user', 'electionType'])
            ->latest('submitted_at')
            ->limit(20)
            ->get();

        $candidates = Candidate::with('electionType')->get();

        return view('dashboard.admin', compact(
            'totalSubmissions', 'verifiedSubmissions', 'pendingSubmissions',
            'totalStations', 'stationsReported', 'totalVotes', 'totalSpoilt',
            'totalRegistered', 'turnout', 'latestSubmission', 'constituencies',
            'electionTypes', 'recentSubmissions', 'candidates'
        ));
    }

    private function agentDashboard()
    {
        $user = Auth::user();
        $mySubmissions = $user->submissions()->with(['pollingStation.ward', 'electionType'])
            ->latest('submitted_at')
            ->get();

        $totalSubmitted = $mySubmissions->count();
        $verified = $mySubmissions->where('status', 'verified')->count();
        $pending = $mySubmissions->where('status', 'pending')->count();
        $rejected = $mySubmissions->where('status', 'rejected')->count();

        $electionTypes = ElectionType::where('is_active', true)->get();

        return view('dashboard.agent', compact(
            'mySubmissions', 'totalSubmitted', 'verified', 'pending', 'rejected',
            'electionTypes'
        ));
    }
}
