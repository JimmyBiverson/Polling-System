@extends('layouts.app')
@section('title', 'Admin Dashboard — Kakamega Tallying Center')

@section('content')
<div class="space-y-6" x-data="dashboard()">

    {{-- Live Tallying Stream Header --}}
    <div class="bg-gradient-to-r from-gray-950 via-emerald-950 to-gray-950 text-white rounded-3xl p-6 sm:p-8 shadow-2xl border-2 border-emerald-600/40 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
            <div>
                <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-emerald-500 text-gray-950 text-xs font-black shadow-lg shadow-emerald-500/20 mb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-950 animate-ping"></span>
                    <span class="uppercase tracking-wide">Live Transmission Stream Active — Kakamega County Tallying Center</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight drop-shadow-sm">Electoral Command & Analytics Center</h1>
                <p class="text-amber-300 text-sm sm:text-base font-bold mt-1.5">Form 34A & 34B Real-time Results Processing (12 Constituencies, 60 Wards)</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button @click="showBulkModal = true" class="bg-emerald-400 hover:bg-emerald-300 text-gray-950 font-black text-xs sm:text-sm py-3 px-6 rounded-2xl shadow-xl shadow-emerald-400/20 transition-all flex items-center gap-2 border border-emerald-300">
                    <svg class="w-5 h-5 text-gray-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                    <span>WhatsApp Bulk Paste</span>
                </button>
                <button @click="location.reload()" class="bg-white hover:bg-gray-100 text-gray-950 font-black text-xs sm:text-sm py-3 px-5 rounded-2xl shadow-lg transition-all flex items-center gap-2 border border-gray-200">
                    <svg class="w-5 h-5 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Refresh Data</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Key Electoral Metrics --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Total Valid Votes</p>
                    <p class="text-3xl font-black text-gray-950 mt-1">{{ number_format($totalVotes) }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center border border-emerald-200">
                    <svg class="w-6 h-6 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-xs font-extrabold">
                <span class="text-emerald-900 bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-300">{{ $verifiedSubmissions }} verified</span>
                <span class="text-amber-900 bg-amber-100 px-2.5 py-1 rounded-lg border border-amber-300">{{ $pendingSubmissions }} pending</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Spoilt / Rejected</p>
                    <p class="text-3xl font-black text-red-600 mt-1">{{ number_format($totalSpoilt) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center border border-red-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-700 font-extrabold">
                @php $spoiltRate = $totalVotes > 0 ? round(($totalSpoilt / ($totalVotes + $totalSpoilt)) * 100, 1) : 0; @endphp
                {{ $spoiltRate }}% of total ballots cast
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Registered Voters</p>
                    <p class="text-3xl font-black text-blue-950 mt-1">{{ number_format($totalRegistered) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center border border-blue-200">
                    <svg class="w-6 h-6 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-700 font-extrabold">
                Across {{ number_format($totalStations) }} polling stations
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Voter Turnout Rate</p>
                    <p class="text-3xl font-black mt-1 {{ $turnout >= 70 ? 'text-emerald-600' : ($turnout >= 50 ? 'text-amber-600' : 'text-red-600') }}">{{ $turnout }}%</p>
                </div>
                <div class="w-12 h-12 {{ $turnout >= 70 ? 'bg-emerald-100 border-emerald-200' : ($turnout >= 50 ? 'bg-amber-100 border-amber-200' : 'bg-red-100 border-red-200') }} border rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 {{ $turnout >= 70 ? 'text-emerald-700' : ($turnout >= 50 ? 'text-amber-700' : 'text-red-600') }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="h-2.5 rounded-full {{ $turnout >= 70 ? 'bg-emerald-600' : ($turnout >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ min($turnout, 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Interactive Graphical Analytics Section: Explicit Graph (Left) & Pie Chart (Right) Layout --}}
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-200 pb-3">
            <div>
                <h2 class="text-xl font-black text-gray-950 flex items-center gap-2">
                    <svg class="w-6 h-6 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Electoral Graphical Analytics & Demographics</span>
                </h2>
                <p class="text-xs text-gray-600 font-semibold">Side-by-Side Visuals: Bar Graphs on the Left, Pie & Doughnut Charts on the Right</p>
            </div>
            <span class="text-xs font-black bg-emerald-100 text-emerald-900 px-3 py-1 rounded-full border border-emerald-300 shadow-sm hidden sm:inline-block">
                Live Interactive Visuals
            </span>
        </div>

        {{-- ROW 1: Governor Candidate Bar Graph (LEFT) vs Voter Demographics Pie Chart (RIGHT) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Graph on LEFT (2 cols on lg screens) --}}
            <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-black text-gray-950 flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-full bg-emerald-600"></span>
                            Governor Candidate Tally Standings (Bar Graph)
                        </h3>
                        <p class="text-xs text-gray-600 font-medium">Real-time candidate vote totals & leading percentages in Kakamega</p>
                    </div>
                    <span class="text-xs font-black bg-emerald-100 text-emerald-900 px-3 py-1 rounded-full border border-emerald-300">
                        Governor Race
                    </span>
                </div>
                <div class="relative h-64 sm:h-72">
                    <canvas id="governorRaceChart"></canvas>
                </div>
                @if(count($governorCandidatesData) > 0)
                <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                    <span class="text-gray-600 font-bold">Leading Candidate:</span>
                    <span class="font-black text-emerald-950 bg-emerald-100 px-3 py-1.5 rounded-xl border border-emerald-300">
                        🏆 {{ $governorCandidatesData[0]['name'] }} ({{ $governorCandidatesData[0]['party'] }}) — {{ number_format($governorCandidatesData[0]['votes']) }} votes ({{ $governorCandidatesData[0]['percentage'] }}%)
                    </span>
                </div>
                @endif
            </div>

            {{-- Pie / Doughnut Chart on RIGHT (1 col) --}}
            <div class="bg-white rounded-3xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-base font-black text-gray-950 flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-full bg-blue-600"></span>
                            Voter Age Demographics (Pie Chart)
                        </h3>
                        <p class="text-xs text-gray-600 font-medium">Voter turnout distribution by age bracket</p>
                    </div>
                    <span class="text-xs font-bold bg-blue-100 text-blue-900 px-2.5 py-0.5 rounded-full border border-blue-200">
                        Demographics
                    </span>
                </div>
                <div class="relative h-56 flex items-center justify-center">
                    <canvas id="demographicsChart"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2 text-xs pt-3 border-t border-gray-100 font-extrabold">
                    <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-xl border border-gray-100">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                        <span class="text-gray-700">Youth (18–25): <strong class="text-gray-950">28%</strong></span>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-xl border border-gray-100">
                        <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>
                        <span class="text-gray-700">Young (26–35): <strong class="text-gray-950">34%</strong></span>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-xl border border-gray-100">
                        <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                        <span class="text-gray-700">Middle (36–55): <strong class="text-gray-950">26%</strong></span>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-xl border border-gray-100">
                        <span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span>
                        <span class="text-gray-700">Seniors (56+): <strong class="text-gray-950">12%</strong></span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ROW 2: Constituency Votes Grouped Bar Graph (LEFT) vs Polling Transmission Gauge (RIGHT) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Grouped Bar Graph on LEFT (2 cols on lg screens) --}}
            <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-black text-gray-950 flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-full bg-amber-600"></span>
                            Constituency Votes & Spoilt Ballots (Grouped Graph)
                        </h3>
                        <p class="text-xs text-gray-600 font-medium">Valid votes cast vs spoilt ballots per constituency in Kakamega</p>
                    </div>
                    <span class="text-xs font-extrabold bg-gray-100 text-gray-900 px-3 py-1 rounded-full border border-gray-300">
                        12 Constituencies
                    </span>
                </div>
                <div class="relative h-64">
                    <canvas id="constituencyChart"></canvas>
                </div>
            </div>

            {{-- Doughnut / Gauge Chart on RIGHT (1 col) --}}
            <div class="bg-white rounded-3xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-base font-black text-gray-950 flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-full bg-indigo-600"></span>
                            Station Transmission Progress (Gauge Chart)
                        </h3>
                        <p class="text-xs text-gray-600 font-medium">Transmitted vs Outstanding Form 34A Reports</p>
                    </div>
                    @php $transPct = $totalStations > 0 ? round(($stationsReported / $totalStations) * 100) : 0; @endphp
                    <span class="text-xs font-black bg-indigo-100 text-indigo-900 px-2.5 py-0.5 rounded-full border border-indigo-200">
                        {{ $transPct }}% Done
                    </span>
                </div>
                <div class="relative h-56 flex items-center justify-center">
                    <canvas id="transmissionGaugeChart"></canvas>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs font-extrabold">
                    <span class="text-emerald-900 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
                        Transmitted: {{ number_format($stationsReported) }}
                    </span>
                    <span class="text-gray-700 bg-gray-100 px-3 py-1.5 rounded-xl border border-gray-300">
                        Remaining: {{ number_format(max($totalStations - $stationsReported, 0)) }}
                    </span>
                </div>
            </div>

        </div>
    </div>

    {{-- Constituency List Breakdown --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-900">Constituency & Ward Breakdown</h2>
            <span class="text-xs text-gray-500 font-medium">Click any constituency to expand ward list</span>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($constituencies as $constituency)
            @php
                $cVotes = 0;
                $cSpoilt = 0;
                $cRegistered = 0;
                $cStations = 0;
                $cReported = 0;
                foreach($constituency->wards as $ward) {
                    foreach($ward->pollingStations as $station) {
                        $cStations++;
                        $latest = $station->submissions->first();
                        if($latest) {
                            $cReported++;
                            $cVotes += $latest->total_votes_cast;
                            $cSpoilt += $latest->spoilt_votes;
                            $cRegistered += $latest->registered_voters;
                        }
                    }
                }
            @endphp
            <div class="px-6 py-4 hover:bg-green-50/30 transition-colors" x-data="{ open: false }">
                <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-700 text-white rounded-xl flex items-center justify-center font-extrabold text-sm shadow-sm">
                            {{ strtoupper(substr($constituency->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-base">{{ $constituency->name }} Constituency</p>
                            <p class="text-xs text-gray-500">{{ $cReported }} / {{ $cStations }} stations transmitted</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6 text-right">
                        <div>
                            <p class="font-extrabold text-gray-900">{{ number_format($cVotes) }}</p>
                            <p class="text-[11px] text-gray-500 uppercase font-medium">votes cast</p>
                        </div>
                        <div>
                            @php $cTurnout = $cRegistered > 0 ? round(($cVotes / $cRegistered) * 100) : 0; @endphp
                            <p class="font-extrabold {{ $cTurnout >= 70 ? 'text-green-600' : 'text-amber-600' }}">{{ $cTurnout }}%</p>
                            <p class="text-[11px] text-gray-500 uppercase font-medium">turnout</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                <div x-show="open" x-collapse class="mt-4 pl-12 space-y-2 border-t border-gray-100 pt-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Wards in {{ $constituency->name }}</p>
                    @foreach($constituency->wards as $ward)
                    <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-xl">
                        <span class="text-sm font-semibold text-gray-800">{{ $ward->name }} Ward</span>
                        <span class="text-xs text-gray-500 bg-white px-2.5 py-1 rounded-lg border border-gray-200">{{ $ward->pollingStations->count() }} stations</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-gray-400">No constituencies loaded.</div>
            @endforelse
        </div>
    </div>

    {{-- Form 34A Recent Transmissions with SHA-256 Audit Verification --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Form 34A Recent Station Transmissions</h2>
                <p class="text-xs text-gray-500">Live incoming polling station reports & verification status</p>
            </div>
            <a href="{{ route('reports.index') }}" class="text-xs font-bold text-green-700 hover:text-green-800 bg-green-50 px-3 py-1.5 rounded-lg border border-green-200">View All Reports →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-3">Station & Ward</th>
                        <th class="px-6 py-3">Presiding Agent</th>
                        <th class="px-6 py-3">Election</th>
                        <th class="px-6 py-3 text-right">Votes Cast</th>
                        <th class="px-6 py-3 text-right">Spoilt</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Submitted</th>
                        <th class="px-6 py-3 text-center">Form 34A Integrity</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentSubmissions as $sub)
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="px-6 py-3.5">
                            <p class="font-bold text-gray-900">{{ $sub->pollingStation->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $sub->pollingStation?->ward?->name ?? '' }} Ward · {{ $sub->pollingStation?->ward?->constituency?->name ?? '' }}</p>
                        </td>
                        <td class="px-6 py-3.5">
                            <p class="font-semibold text-gray-800">{{ $sub->agent_name }}</p>
                            <p class="text-[11px] text-gray-400 font-mono">{{ $sub->agent_code ?? 'N/A' }}</p>
                        </td>
                        <td class="px-6 py-3.5 font-medium text-gray-700">{{ $sub->electionType->name ?? 'Governor' }}</td>
                        <td class="px-6 py-3.5 text-right font-extrabold text-gray-900">{{ number_format($sub->total_votes_cast) }}</td>
                        <td class="px-6 py-3.5 text-right font-semibold text-red-600">{{ number_format($sub->spoilt_votes) }}</td>
                        <td class="px-6 py-3.5">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'verified' => 'bg-green-100 text-green-800 border-green-200',
                                    'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                    'disputed' => 'bg-purple-100 text-purple-800 border-purple-200',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusColors[$sub->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($sub->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-xs text-gray-500">{{ $sub->submitted_at->diffForHumans() }}</td>
                        <td class="px-6 py-3.5 text-center">
                            <a href="{{ route('votes.show', $sub) }}" class="inline-flex items-center gap-1 text-xs font-bold text-green-700 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg border border-green-200 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>Inspect Form 34A</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-6 py-8 text-center text-gray-400">No transmissions received yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- WhatsApp Bulk Ingestion Modal --}}
    <div x-show="showBulkModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl space-y-5 relative" @click.away="showBulkModal = false">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">WhatsApp Bulk Result Ingestion</h3>
                    <p class="text-xs text-gray-500">Paste structured WhatsApp result lines for instant validation & database processing</p>
                </div>
                <button @click="showBulkModal = false" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-xl hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('votes.bulk') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Target Election Type</label>
                    <select name="election_type_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 bg-gray-50">
                        @foreach($electionTypes as $et)
                        <option value="{{ $et->id }}">{{ $et->name }} Election</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Bulk Data Text Lines</label>
                        <button type="button" @click="copyTemplate()" class="text-xs font-semibold text-green-700 hover:underline flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span>Copy Template</span>
                        </button>
                    </div>
                    <textarea name="bulk_data" rows="6" x-model="bulkText" required placeholder="Lurambi, Sheywe, Bondeni Primary School, Alice M, E0987654-0701092708-74225, PO James, Fernandes Barasa:340, Cleophas Malala:210, Spoilt:12, Total:562, Registered:800"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-xs font-mono focus:ring-2 focus:ring-green-500 bg-gray-50"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" @click="showBulkModal = false" class="px-5 py-2.5 text-xs font-semibold text-gray-500 hover:bg-gray-100 rounded-xl">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-green-700 hover:bg-green-800 text-white font-bold text-xs rounded-xl shadow-lg shadow-green-700/25">Process Bulk Data</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function dashboard() {
    return {
        showBulkModal: false,
        bulkText: '',
        copyTemplate() {
            const tmpl = "Lurambi, Sheywe, Bondeni Primary School, Alice Mwangi, E0987654-0701092708-74225, PO James, Fernandes Barasa:340, Cleophas Malala:210, Spoilt:12, Total:562, Registered:800\n" +
                         "Lurambi, Shirere, Musaa Primary School, Bob Ochieng, E0987655-0722092708-74226, PO Sarah, Fernandes Barasa:280, Cleophas Malala:195, Spoilt:8, Total:483, Registered:750";
            this.bulkText = tmpl;
            navigator.clipboard.writeText(tmpl);
            alert("WhatsApp bulk template copied to clipboard & filled!");
        },
        init() {
            this.initCharts();
        },
        initCharts() {
            // 1. Governor Candidate Lead Race Chart
            const govData = @json($governorCandidatesData);
            const govCtx = document.getElementById('governorRaceChart')?.getContext('2d');
            if (govCtx && govData.length > 0) {
                new Chart(govCtx, {
                    type: 'bar',
                    data: {
                        labels: govData.map(c => c.name + ' (' + c.party + ')'),
                        datasets: [{
                            label: 'Total Votes Tally',
                            data: govData.map(c => c.votes),
                            backgroundColor: ['#15803d', '#2563eb', '#d97706', '#9333ea', '#dc2626'],
                            borderRadius: 10,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        return ctx.raw.toLocaleString() + ' votes (' + govData[ctx.dataIndex].percentage + '%)';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // 2. Voter Demographics Chart
            const demoData = @json($demographics);
            const demoCtx = document.getElementById('demographicsChart')?.getContext('2d');
            if (demoCtx) {
                new Chart(demoCtx, {
                    type: 'doughnut',
                    data: {
                        labels: demoData.labels,
                        datasets: [{
                            data: demoData.data,
                            backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#a855f7'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        cutout: '68%'
                    }
                });
            }

            // 3. Station Transmission Status Gauge
            const transCtx = document.getElementById('transmissionGaugeChart')?.getContext('2d');
            if (transCtx) {
                new Chart(transCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Transmitted', 'Remaining'],
                        datasets: [{
                            data: [{{ $stationsReported }}, {{ max($totalStations - $stationsReported, 0) }}],
                            backgroundColor: ['#16a34a', '#e5e7eb'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        cutout: '75%'
                    }
                });
            }

            // 4. Constituency Cast Votes vs Spoilt Votes
            const constSum = @json($constituencySummary);
            const constCtx = document.getElementById('constituencyChart')?.getContext('2d');
            if (constCtx && constSum.length > 0) {
                new Chart(constCtx, {
                    type: 'bar',
                    data: {
                        labels: constSum.map(c => c.name),
                        datasets: [
                            {
                                label: 'Votes Cast',
                                data: constSum.map(c => c.votes_cast),
                                backgroundColor: '#16a34a',
                                borderRadius: 6
                            },
                            {
                                label: 'Spoilt Votes',
                                data: constSum.map(c => c.spoilt_votes),
                                backgroundColor: '#ef4444',
                                borderRadius: 6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        }
    };
}
</script>
@endpush
