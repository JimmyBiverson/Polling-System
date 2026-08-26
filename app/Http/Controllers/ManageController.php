<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Constituency;
use App\Models\County;
use App\Models\ElectionType;
use App\Models\PollingStation;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageController extends Controller
{
    public function index()
    {
        $counties = County::all();
        $constituencies = Constituency::with('county')->get();
        $wards = Ward::with('constituency')->get();
        $stations = PollingStation::with('ward')->get();
        $candidates = Candidate::with('electionType')->get();
        $electionTypes = ElectionType::all();

        return view('manage.index', compact(
            'counties', 'constituencies', 'wards', 'stations',
            'candidates', 'electionTypes'
        ));
    }

    // ── Counties ──────────────────────────────────────────

    public function storeCounty(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:counties,name']);
        County::create($request->only('name', 'code'));
        return back()->with('success', 'County created.');
    }

    public function destroyCounty(County $county)
    {
        $county->delete();
        return back()->with('success', 'County deleted.');
    }

    // ── Constituencies ────────────────────────────────────

    public function storeConstituency(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'county_id' => 'required|exists:counties,id',
        ]);
        Constituency::create($request->only('name', 'code', 'county_id'));
        return back()->with('success', 'Constituency created.');
    }

    public function destroyConstituency(Constituency $constituency)
    {
        $constituency->delete();
        return back()->with('success', 'Constituency deleted.');
    }

    // ── Wards ─────────────────────────────────────────────

    public function storeWard(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'constituency_id' => 'required|exists:constituencies,id',
        ]);
        Ward::create($request->only('name', 'code', 'constituency_id'));
        return back()->with('success', 'Ward created.');
    }

    public function destroyWard(Ward $ward)
    {
        $ward->delete();
        return back()->with('success', 'Ward deleted.');
    }

    // ── Polling Stations ──────────────────────────────────

    public function storeStation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ward_id' => 'required|exists:wards,id',
            'registered_voters' => 'nullable|integer|min:0',
        ]);
        PollingStation::create($request->only('name', 'code', 'ward_id', 'presiding_officer', 'registered_voters'));
        return back()->with('success', 'Polling station created.');
    }

    public function destroyStation(PollingStation $station)
    {
        $station->delete();
        return back()->with('success', 'Station deleted.');
    }

    // ── Candidates ────────────────────────────────────────

    public function storeCandidate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'election_type_id' => 'required|exists:election_types,id',
        ]);
        Candidate::create($request->only('name', 'party', 'election_type_id'));
        return back()->with('success', 'Candidate added.');
    }

    public function destroyCandidate(Candidate $candidate)
    {
        $candidate->delete();
        return back()->with('success', 'Candidate deleted.');
    }

    // ── Election Types ────────────────────────────────────

    public function storeElectionType(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        ElectionType::create($request->only('name'));
        return back()->with('success', 'Election type created.');
    }

    public function destroyElectionType(ElectionType $electionType)
    {
        $electionType->delete();
        return back()->with('success', 'Election type deleted.');
    }
}
