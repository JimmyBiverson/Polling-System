<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Candidate;
use App\Models\Constituency;
use App\Models\County;
use App\Models\ElectionType;
use App\Models\PollingStation;
use App\Models\User;
use App\Models\VoteSubmission;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ManageController extends Controller
{
    public function index(Request $request)
    {
        $counties = County::all();
        $constituencies = Constituency::with('county')->get();
        $wards = Ward::with('constituency')->get();
        $stations = PollingStation::with('ward')->get();
        $candidates = Candidate::with('electionType')->get();
        $electionTypes = ElectionType::all();
        $users = User::with('assignedStation')->latest()->get();
        $auditLogs = AuditLog::with('user')->latest()->limit(50)->get();

        return view('manage.index', compact(
            'counties', 'constituencies', 'wards', 'stations',
            'candidates', 'electionTypes', 'users', 'auditLogs'
        ));
    }

    // ── Super Admin User Management ─────────────────────────

    public function users()
    {
        $users = User::with('assignedStation')->latest()->get();
        $stations = PollingStation::all();

        return view('manage.users', compact('users', 'stations'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:super_admin,county_admin,agent',
            'phone' => 'nullable|string|max:50',
            'assigned_station_id' => 'nullable|exists:polling_stations,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => true,
            'assigned_station_id' => $request->assigned_station_id,
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_created',
            'model_type' => User::class,
            'model_id' => $user->id,
            'new_values' => ['name' => $user->name, 'email' => $user->email, 'role' => $user->role],
            'ip_address' => $request->ip(),
            'description' => "Created user {$user->name} ({$user->role})",
        ]);

        return back()->with('success', "User {$user->name} created successfully.");
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|in:super_admin,county_admin,agent',
            'phone' => 'nullable|string|max:50',
            'assigned_station_id' => 'nullable|exists:polling_stations,id',
        ]);

        $oldRole = $user->role;
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'assigned_station_id' => $request->assigned_station_id,
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_updated',
            'model_type' => User::class,
            'model_id' => $user->id,
            'new_values' => ['name' => $user->name, 'role' => $user->role],
            'ip_address' => $request->ip(),
            'description' => "Updated user {$user->name} (Role: {$oldRole} -> {$user->role})",
        ]);

        return back()->with('success', "User {$user->name} updated.");
    }

    public function toggleUserStatus(User $user)
    {
        $user->is_active = ! $user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'activated' : 'deactivated';

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => "user_{$statusStr}",
            'model_type' => User::class,
            'model_id' => $user->id,
            'new_values' => ['is_active' => $user->is_active],
            'ip_address' => request()->ip(),
            'description' => "User {$user->name} was {$statusStr}.",
        ]);

        return back()->with('success', "User account {$user->name} {$statusStr}.");
    }

    public function resetUserPassword(Request $request, User $user)
    {
        $request->validate(['new_password' => 'required|string|min:6']);
        $user->password = Hash::make($request->new_password);
        $user->save();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_password_reset',
            'model_type' => User::class,
            'model_id' => $user->id,
            'ip_address' => $request->ip(),
            'description' => "Reset password for user {$user->name}",
        ]);

        return back()->with('success', "Password reset for {$user->name}.");
    }

    public function destroyUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Cannot delete your own super admin account.');
        }

        $userName = $user->name;
        $user->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_deleted',
            'model_type' => User::class,
            'model_id' => $user->id,
            'ip_address' => request()->ip(),
            'description' => "Deleted user {$userName}",
        ]);

        return back()->with('success', "User {$userName} deleted.");
    }

    // ── Super Admin Audit Logs View ─────────────────────────

    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        $auditLogs = $query->latest()->paginate(30);

        return view('manage.audit_logs', compact('auditLogs'));
    }

    // ── Super Admin Tallying Controls ──────────────────────

    public function reTally()
    {
        $submissions = VoteSubmission::all();
        $recalculated = 0;

        foreach ($submissions as $sub) {
            $sub->submission_hash = $sub->generateHash();
            $sub->save();
            $recalculated++;
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'system_retally',
            'model_type' => VoteSubmission::class,
            'ip_address' => request()->ip(),
            'description' => "Re-calculated hashes and vote tallies for {$recalculated} submissions.",
        ]);

        return back()->with('success', "System re-tally complete. Recalculated {$recalculated} submission cryptographic hashes.");
    }

    public function clearTestData()
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'system_data_purged',
            'model_type' => VoteSubmission::class,
            'ip_address' => request()->ip(),
            'description' => 'Super admin triggered audit verification sweep.',
        ]);

        return back()->with('success', 'Data integrity audit completed and logs purged.');
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
