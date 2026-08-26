@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6" x-data="dashboard()">

    {{-- Hero Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Votes</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalVotes) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-xs">
                <span class="text-green-600 font-medium">{{ $verifiedSubmissions }} verified</span>
                <span class="text-gray-400">·</span>
                <span class="text-amber-600 font-medium">{{ $pendingSubmissions }} pending</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Spoilt Votes</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ number_format($totalSpoilt) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500">
                @php $spoiltRate = $totalVotes > 0 ? round(($totalSpoilt / ($totalVotes + $totalSpoilt)) * 100, 1) : 0; @endphp
                {{ $spoiltRate }}% of total cast
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalRegistered) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500">
                Across {{ number_format($totalStations) }} stations
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Turnout</p>
                    <p class="text-3xl font-bold mt-1 {{ $turnout >= 70 ? 'text-green-600' : ($turnout >= 50 ? 'text-amber-600' : 'text-red-600') }}">{{ $turnout }}%</p>
                </div>
                <div class="w-12 h-12 {{ $turnout >= 70 ? 'bg-green-100' : ($turnout >= 50 ? 'bg-amber-100' : 'bg-red-100') }} rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 {{ $turnout >= 70 ? 'text-green-600' : ($turnout >= 50 ? 'text-amber-600' : 'text-red-600') }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $turnout >= 70 ? 'bg-green-500' : ($turnout >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ min($turnout, 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Coverage --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900">Reporting Coverage</h2>
            <span class="text-sm text-gray-500">{{ $stationsReported }} / {{ $totalStations }} stations</span>
        </div>
        @php $coverage = $totalStations > 0 ? round(($stationsReported / $totalStations) * 100, 1) : 0; @endphp
        <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden">
            <div class="h-4 rounded-full bg-gradient-to-r from-green-600 to-green-500 transition-all duration-1000 flex items-center justify-end pr-2" style="width: {{ max($coverage, 5) }}%">
                @if($coverage > 10)
                <span class="text-xs text-white font-bold">{{ $coverage }}%</span>
                @endif
            </div>
        </div>
        @if($latestSubmission)
        <p class="text-xs text-gray-500 mt-3">
            Last report from <strong>{{ $latestSubmission->pollingStation->name ?? 'Unknown' }}</strong>
            — {{ $latestSubmission->submitted_at->diffForHumans() }}
        </p>
        @endif
    </div>

    {{-- Constituency Breakdown --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Constituency Breakdown</h2>
        </div>
        <div class="divide-y divide-gray-50">
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
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors" x-data="{ open: false }">
                <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-green-700 font-bold text-sm">
                            {{ strtoupper(substr($constituency->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $constituency->name }}</p>
                            <p class="text-xs text-gray-500">{{ $cReported }}/{{ $cStations }} stations reported</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6 text-right">
                        <div>
                            <p class="font-bold text-gray-900">{{ number_format($cVotes) }}</p>
                            <p class="text-xs text-gray-500">votes</p>
                        </div>
                        <div>
                            @php $cTurnout = $cRegistered > 0 ? round(($cVotes / $cRegistered) * 100) : 0; @endphp
                            <p class="font-bold {{ $cTurnout >= 70 ? 'text-green-600' : 'text-amber-600' }}">{{ $cTurnout }}%</p>
                            <p class="text-xs text-gray-500">turnout</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                <div x-show="open" x-collapse class="mt-4 pl-13">
                    @foreach($constituency->wards as $ward)
                    <div class="flex items-center justify-between py-2 border-t border-gray-100 first:border-0">
                        <span class="text-sm text-gray-700">{{ $ward->name }}</span>
                        <span class="text-sm text-gray-500">{{ $ward->pollingStations->count() }} stations</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-gray-400">No constituencies found.</div>
            @endforelse
        </div>
    </div>

    {{-- Recent Submissions --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Recent Submissions</h2>
            <a href="{{ route('reports.index') }}" class="text-sm text-green-700 font-medium hover:underline">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-6 py-3 font-semibold text-gray-600">Station</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Agent</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Election</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Votes</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Status</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Time</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentSubmissions as $sub)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-900">{{ $sub->pollingStation->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $sub->pollingStation?->ward?->name ?? '' }}</p>
                        </td>
                        <td class="px-6 py-3 text-gray-700">{{ $sub->agent_name }}</td>
                        <td class="px-6 py-3 text-gray-700">{{ $sub->electionType->name ?? 'N/A' }}</td>
                        <td class="px-6 py-3 font-semibold text-gray-900">{{ number_format($sub->total_votes_cast) }}</td>
                        <td class="px-6 py-3">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-amber-100 text-amber-800',
                                    'verified' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'disputed' => 'bg-purple-100 text-purple-800',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$sub->status] ?? '' }}">
                                {{ ucfirst($sub->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500 text-xs">{{ $sub->submitted_at->diffForHumans() }}</td>
                        <td class="px-6 py-3">
                            <a href="{{ route('votes.show', $sub) }}" class="text-green-700 hover:underline text-xs font-medium">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400">No submissions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function dashboard() {
    return {
        init() {
            // Auto-refresh every 30 seconds
            setInterval(() => {
                fetch('/api/live-stats')
                    .then(r => r.json())
                    .then(data => {
                        // Could update stats in real-time here
                    })
                    .catch(() => {});
            }, 30000);
        }
    };
}
</script>
@endpush
