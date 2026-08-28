@extends('layouts.app')
@section('title', 'Manage System')

@section('content')
<div class="space-y-6" x-data="{ tab: 'users' }">

    <div class="bg-gradient-to-r from-gray-950 via-emerald-950 to-gray-950 text-white rounded-3xl p-6 sm:p-8 shadow-2xl border-2 border-amber-500/40 relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 relative z-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-400 text-gray-950 text-xs font-black shadow-md mb-3">
                    <svg class="w-4 h-4 text-gray-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span class="uppercase tracking-wide">Super Admin Governance & Operations</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight">System Data & User Security Hub</h1>
                <p class="text-amber-300 text-sm sm:text-base font-bold mt-1.5">Manage field agents, administrative credentials, audit trails, and electoral boundaries.</p>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('manage.reTally') }}" onsubmit="return confirm('Execute system-wide cryptographic hash re-tallying?')">
                    @csrf
                    <button type="submit" class="bg-amber-400 hover:bg-amber-300 text-gray-950 font-black text-xs sm:text-sm py-3 px-5 rounded-2xl shadow-xl border border-amber-300 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>System Re-Tally</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 overflow-x-auto border-b border-gray-200 pb-px scrollbar-hide">
        @foreach([
            'users' => '👥 User & Access Control',
            'audit_logs' => '🛡️ Security Audit Logs',
            'constituencies' => '🏛️ Constituencies',
            'wards' => '📍 Wards',
            'stations' => '🏫 Polling Stations',
            'candidates' => '👔 Candidates',
            'election_types' => '🗳️ Election Types'
        ] as $key => $label)
        <button @click="tab = '{{ $key }}'"
                class="px-4 py-3 text-sm font-extrabold rounded-t-2xl transition-all whitespace-nowrap border-t border-x border-transparent"
                :class="tab === '{{ $key }}' ? 'bg-emerald-800 text-white shadow-md border-emerald-900' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-950'">
            {{ $label }}
        </button>
        @endforeach
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        {{ session('success') }}
    </div>
    @endif

    {{-- 1. Users & Access Control --}}
    <div x-show="tab === 'users'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden space-y-6 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">User Accounts & Role Administration</h3>
                <p class="text-xs text-gray-500">Create new accounts, assign roles, toggle access status, and reset passwords</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-semibold">
                <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-lg border border-amber-200">{{ $users->where('role', 'super_admin')->count() }} Super Admins</span>
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-lg border border-blue-200">{{ $users->where('role', 'county_admin')->count() }} County Admins</span>
                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-lg border border-gray-200">{{ $users->where('role', 'agent')->count() }} Field Agents</span>
            </div>
        </div>

        {{-- Add User Form --}}
        <div class="bg-gray-50/80 rounded-2xl p-5 border border-gray-100">
            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Create New System User / Agent</h4>
            <form method="POST" action="{{ route('manage.users.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Full Name</label>
                    <input type="text" name="name" required placeholder="e.g. John Agent" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Email / Username</label>
                    <input type="text" name="email" required placeholder="e.g. agent5@polling.go.ke or agent5" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Role Assignment</label>
                    <select name="role" required class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 bg-white">
                        <option value="agent">Field Agent (Submission Only)</option>
                        <option value="county_admin">County Admin (Verification & Reports)</option>
                        <option value="super_admin">Super Admin (Full Governance Rights)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Phone Number</label>
                    <input type="text" name="phone" placeholder="+254 700 000 000" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Assigned Polling Station</label>
                    <select name="assigned_station_id" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 bg-white">
                        <option value="">-- No Fixed Station --</option>
                        @foreach($stations->take(100) as $st)
                        <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->ward->name ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2 lg:col-span-3 flex justify-end">
                    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white font-bold text-xs py-2.5 px-6 rounded-xl shadow-md transition-all">
                        + Create Account
                    </button>
                </div>
            </form>
        </div>

        {{-- Users Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3">User Details</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Assigned Station</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $u)
                    <tr class="hover:bg-gray-50/80 transition-colors" x-data="{ showReset: false }">
                        <td class="px-4 py-3">
                            <p class="font-bold text-gray-900">{{ $u->name }}</p>
                            <p class="text-xs text-gray-500 font-mono">{{ $u->email }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($u->role === 'super_admin')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                                👑 Super Admin
                            </span>
                            @elseif($u->role === 'county_admin')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                🏢 County Admin
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                👤 Field Agent
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            {{ $u->assignedStation->name ?? 'Unassigned' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($u->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-green-100 text-green-800 border border-green-200">Active</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-red-100 text-red-800 border border-red-200">Disabled</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('manage.users.toggleStatus', $u) }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold px-2.5 py-1 rounded-lg border {{ $u->is_active ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' : 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100' }}">
                                        {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                <button type="button" @click="showReset = !showReset" class="text-xs font-semibold bg-gray-50 hover:bg-gray-100 text-gray-700 px-2.5 py-1 rounded-lg border border-gray-200">
                                    Reset Password
                                </button>
                                @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('manage.users.destroy', $u) }}" onsubmit="return confirm('Permanently delete user {{ $u->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold bg-red-50 hover:bg-red-100 text-red-600 px-2.5 py-1 rounded-lg border border-red-200">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </div>

                            {{-- Password Reset Modal Inline --}}
                            <div x-show="showReset" class="mt-2 p-3 bg-gray-50 rounded-xl border border-gray-200 text-left">
                                <form method="POST" action="{{ route('manage.users.resetPassword', $u) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="password" name="new_password" required placeholder="New password" class="px-3 py-1.5 text-xs border border-gray-200 rounded-lg flex-1">
                                    <button type="submit" class="bg-gray-900 text-white text-xs font-bold px-3 py-1.5 rounded-lg">Save</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- 2. Security & Audit Logs --}}
    <div x-show="tab === 'audit_logs'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">System Security Audit Trail</h3>
                <p class="text-xs text-gray-500">Immutable record of votes submitted, verifications, user edits, and administrative overrides</p>
            </div>
            <span class="text-xs font-semibold bg-green-50 text-green-700 px-3 py-1 rounded-full border border-green-200">
                Live Audit Stream
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3">Timestamp</th>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Action</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3 text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($auditLogs as $log)
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="px-4 py-3 text-xs text-gray-500 font-mono">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-800 text-xs">{{ $log->user->name ?? 'System' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold border
                                {{ str_contains($log->action, 'override') || str_contains($log->action, 'purged') ? 'bg-purple-100 text-purple-800 border-purple-200' :
                                  (str_contains($log->action, 'submitted') ? 'bg-blue-100 text-blue-800 border-blue-200' :
                                  (str_contains($log->action, 'verified') ? 'bg-green-100 text-green-800 border-green-200' : 'bg-gray-100 text-gray-800 border-gray-200')) }}">
                                {{ strtoupper($log->action) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-700">{{ $log->description }}</td>
                        <td class="px-4 py-3 text-right text-xs font-mono text-gray-400">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">No audit log entries recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Constituencies --}}
    <div x-show="tab === 'constituencies'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Constituencies</h3>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('manage.constituencies.store') }}" class="flex gap-3 mb-6">
                @csrf
                <select name="county_id" class="px-3 py-2 border border-gray-200 rounded-xl text-sm" required>
                    @foreach($counties as $county)
                    <option value="{{ $county->id }}">{{ $county->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="name" placeholder="Constituency name" required class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500">
                <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-green-800 transition-colors">Add</button>
            </form>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="bg-gray-50"><th class="px-4 py-2 text-left font-semibold text-gray-600">ID</th><th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th><th class="px-4 py-2 text-left font-semibold text-gray-600">County</th><th class="px-4 py-2 text-left font-semibold text-gray-600">Wards</th><th class="px-4 py-2 text-right font-semibold text-gray-600">Action</th></tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($constituencies as $c)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-gray-500">{{ $c->id }}</td>
                            <td class="px-4 py-2 font-medium text-gray-900">{{ $c->name }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $c->county->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $c->wards->count() }}</td>
                            <td class="px-4 py-2 text-right">
                                <form method="POST" action="{{ route('manage.constituencies.destroy', $c) }}" onsubmit="return confirm('Delete this constituency?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">No constituencies.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Wards --}}
    <div x-show="tab === 'wards'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-bold text-gray-900">Wards</h3></div>
        <div class="p-6">
            <form method="POST" action="{{ route('manage.wards.store') }}" class="flex gap-3 mb-6">
                @csrf
                <select name="constituency_id" class="px-3 py-2 border border-gray-200 rounded-xl text-sm" required>
                    @foreach($constituencies as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="name" placeholder="Ward name" required class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500">
                <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-green-800 transition-colors">Add</button>
            </form>
            <div class="overflow-x-auto max-h-96">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-gray-50"><tr><th class="px-4 py-2 text-left font-semibold text-gray-600">ID</th><th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th><th class="px-4 py-2 text-left font-semibold text-gray-600">Constituency</th><th class="px-4 py-2 text-left font-semibold text-gray-600">Stations</th><th class="px-4 py-2 text-right font-semibold text-gray-600">Action</th></tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($wards as $w)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-gray-500">{{ $w->id }}</td>
                            <td class="px-4 py-2 font-medium text-gray-900">{{ $w->name }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $w->constituency->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $w->pollingStations->count() }}</td>
                            <td class="px-4 py-2 text-right">
                                <form method="POST" action="{{ route('manage.wards.destroy', $w) }}" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">No wards.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Stations --}}
    <div x-show="tab === 'stations'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-bold text-gray-900">Polling Stations</h3></div>
        <div class="p-6">
            <form method="POST" action="{{ route('manage.stations.store') }}" class="flex gap-3 mb-6">
                @csrf
                <select name="ward_id" class="px-3 py-2 border border-gray-200 rounded-xl text-sm" required>
                    @foreach($wards as $w)
                    <option value="{{ $w->id }}">{{ $w->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="name" placeholder="Station name" required class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500">
                <input type="number" name="registered_voters" placeholder="Reg. voters" class="w-28 px-3 py-2 border border-gray-200 rounded-xl text-sm">
                <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-green-800 transition-colors">Add</button>
            </form>
            <div class="overflow-x-auto max-h-96">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-gray-50"><tr><th class="px-4 py-2 text-left font-semibold text-gray-600">ID</th><th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th><th class="px-4 py-2 text-left font-semibold text-gray-600">Ward</th><th class="px-4 py-2 text-left font-semibold text-gray-600">Reg. Voters</th><th class="px-4 py-2 text-right font-semibold text-gray-600">Action</th></tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($stations->take(200) as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-gray-500">{{ $s->id }}</td>
                            <td class="px-4 py-2 font-medium text-gray-900">{{ $s->name }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $s->ward->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ number_format($s->registered_voters) }}</td>
                            <td class="px-4 py-2 text-right">
                                <form method="POST" action="{{ route('manage.stations.destroy', $s) }}" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">No stations.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($stations->count() > 200)
            <p class="text-xs text-gray-400 mt-3">Showing 200 of {{ $stations->count() }} stations.</p>
            @endif
        </div>
    </div>

    {{-- Candidates --}}
    <div x-show="tab === 'candidates'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-bold text-gray-900">Candidates</h3></div>
        <div class="p-6">
            <form method="POST" action="{{ route('manage.candidates.store') }}" class="flex gap-3 mb-6">
                @csrf
                <select name="election_type_id" class="px-3 py-2 border border-gray-200 rounded-xl text-sm" required>
                    @foreach($electionTypes as $et)
                    <option value="{{ $et->id }}">{{ $et->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="name" placeholder="Candidate name" required class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500">
                <input type="text" name="party" placeholder="Party (optional)" class="w-40 px-3 py-2 border border-gray-200 rounded-xl text-sm">
                <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-green-800 transition-colors">Add</button>
            </form>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="bg-gray-50"><th class="px-4 py-2 text-left font-semibold text-gray-600">ID</th><th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th><th class="px-4 py-2 text-left font-semibold text-gray-600">Party</th><th class="px-4 py-2 text-left font-semibold text-gray-600">Election Type</th><th class="px-4 py-2 text-right font-semibold text-gray-600">Action</th></tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($candidates as $c)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-gray-500">{{ $c->id }}</td>
                            <td class="px-4 py-2 font-medium text-gray-900">{{ $c->name }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $c->party ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $c->electionType->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-right">
                                <form method="POST" action="{{ route('manage.candidates.destroy', $c) }}" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">No candidates.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Election Types --}}
    <div x-show="tab === 'election_types'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-bold text-gray-900">Election Types</h3></div>
        <div class="p-6">
            <form method="POST" action="{{ route('manage.electionTypes.store') }}" class="flex gap-3 mb-6">
                @csrf
                <input type="text" name="name" placeholder="Election type name" required class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500">
                <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-green-800 transition-colors">Add</button>
            </form>
            <div class="space-y-2">
                @forelse($electionTypes as $et)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="font-medium text-gray-900">{{ $et->name }}</span>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500">{{ $et->candidates->count() }} candidates</span>
                        <form method="POST" action="{{ route('manage.electionTypes.destroy', $et) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-gray-400 text-center py-4">No election types.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
