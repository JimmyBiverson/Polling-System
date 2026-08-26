@extends('layouts.app')
@section('title', 'Manage System')

@section('content')
<div class="space-y-6" x-data="{ tab: 'constituencies' }">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Manage System Data</h1>
        <p class="text-sm text-gray-500 mt-1">Add, view, and remove constituencies, wards, stations, candidates, and election types.</p>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 overflow-x-auto border-b border-gray-200 pb-px scrollbar-hide">
        @foreach(['constituencies' => 'Constituencies', 'wards' => 'Wards', 'stations' => 'Stations', 'candidates' => 'Candidates', 'election_types' => 'Election Types'] as $key => $label)
        <button @click="tab = '{{ $key }}'"
                class="px-4 py-2.5 text-sm font-medium rounded-t-xl transition-all whitespace-nowrap"
                :class="tab === '{{ $key }}' ? 'bg-green-700 text-white' : 'text-gray-500 hover:bg-gray-100'">
            {{ $label }}
        </button>
        @endforeach
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        {{ session('success') }}
    </div>
    @endif

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
