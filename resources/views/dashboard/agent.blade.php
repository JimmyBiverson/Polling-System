@extends('layouts.app')
@section('title', 'Agent Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-br from-green-700 to-green-800 rounded-2xl p-6 text-white shadow-xl">
        <h2 class="text-2xl font-bold">Welcome, {{ auth()->user()->name }}</h2>
        <p class="text-green-100 mt-1">Submit and track your polling station reports below.</p>
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="{{ route('votes.create') }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-xl text-sm font-medium transition-all backdrop-blur-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Submit New Report
            </a>
        </div>
    </div>

    {{-- My Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm text-center">
            <p class="text-3xl font-bold text-gray-900">{{ $totalSubmitted }}</p>
            <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Total Submitted</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm text-center">
            <p class="text-3xl font-bold text-green-600">{{ $verified }}</p>
            <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Verified</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm text-center">
            <p class="text-3xl font-bold text-amber-600">{{ $pending }}</p>
            <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Pending</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm text-center">
            <p class="text-3xl font-bold text-red-600">{{ $rejected }}</p>
            <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Rejected</p>
        </div>
    </div>

    {{-- My Submissions --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">My Submissions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-6 py-3 font-semibold text-gray-600">#</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Station</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Election</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Votes</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Status</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($mySubmissions as $sub)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 text-gray-500">#{{ $sub->id }}</td>
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $sub->pollingStation->name ?? 'N/A' }}</td>
                        <td class="px-6 py-3 text-gray-700">{{ $sub->electionType->name ?? 'N/A' }}</td>
                        <td class="px-6 py-3 font-semibold text-gray-900">{{ number_format($sub->total_votes_cast) }}</td>
                        <td class="px-6 py-3">
                            @php
                                $sc = ['pending'=>'bg-amber-100 text-amber-800','verified'=>'bg-green-100 text-green-800','rejected'=>'bg-red-100 text-red-800'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc[$sub->status] ?? '' }}">
                                {{ ucfirst($sub->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500 text-xs">{{ $sub->submitted_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="font-medium">No submissions yet</p>
                            <p class="text-sm mt-1">Start by submitting your first report</p>
                            <a href="{{ route('votes.create') }}" class="inline-flex items-center gap-2 mt-4 bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-green-800 transition-colors">
                                Submit Report
                            </a>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
