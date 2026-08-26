@extends('layouts.app')
@section('title', 'Submission #' . $submission->id)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Submission #{{ $submission->id }}</h1>
            <p class="text-sm text-gray-500">{{ $submission->pollingStation->name ?? 'N/A' }} — {{ $submission->submitted_at->format('d M Y H:i') }}</p>
        </div>
    </div>

    {{-- Status --}}
    @php
        $statusConfig = [
            'pending' => ['bg-amber-50', 'border-amber-200', 'text-amber-800', 'Pending Review'],
            'verified' => ['bg-green-50', 'border-green-200', 'text-green-800', 'Verified ✓'],
            'rejected' => ['bg-red-50', 'border-red-200', 'text-red-800', 'Rejected ✕'],
            'disputed' => ['bg-purple-50', 'border-purple-200', 'text-purple-800', 'Disputed'],
        ];
        $sc = $statusConfig[$submission->status] ?? $statusConfig['pending'];
    @endphp
    <div class="{{ $sc[0] }} border {{ $sc[1] }} rounded-2xl p-5">
        <div class="flex items-center justify-between">
            <span class="text-lg font-bold {{ $sc[2] }}">{{ $sc[3] }}</span>
            @if($submission->submission_hash)
            <span class="text-xs font-mono text-gray-500 bg-white px-3 py-1 rounded-lg">Hash: {{ substr($submission->submission_hash, 0, 16) }}...</span>
            @endif
        </div>
    </div>

    {{-- Station Info --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Station Information</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">Station:</span> <span class="font-semibold text-gray-900">{{ $submission->pollingStation->name ?? 'N/A' }}</span></div>
            <div><span class="text-gray-500">Ward:</span> <span class="font-semibold text-gray-900">{{ $submission->pollingStation?->ward?->name ?? 'N/A' }}</span></div>
            <div><span class="text-gray-500">Constituency:</span> <span class="font-semibold text-gray-900">{{ $submission->pollingStation?->ward?->constituency?->name ?? 'N/A' }}</span></div>
            <div><span class="text-gray-500">Election Type:</span> <span class="font-semibold text-gray-900">{{ $submission->electionType->name ?? 'N/A' }}</span></div>
            <div><span class="text-gray-500">Agent:</span> <span class="font-semibold text-gray-900">{{ $submission->agent_name }}</span></div>
            <div><span class="text-gray-500">Agent Code:</span> <span class="font-mono text-xs text-gray-700">{{ $submission->agent_code }}</span></div>
            <div><span class="text-gray-500">Presiding Officer:</span> <span class="font-semibold text-gray-900">{{ $submission->presiding_officer ?? 'N/A' }}</span></div>
            <div><span class="text-gray-500">Submitted:</span> <span class="font-semibold text-gray-900">{{ $submission->submitted_at->format('d M Y H:i:s') }}</span></div>
        </div>
    </div>

    {{-- Vote Results --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Vote Results</h3>
        <div class="space-y-3">
            @foreach($submission->details as $detail)
            @php $pct = $submission->total_votes_cast > 0 ? round(($detail->votes / $submission->total_votes_cast) * 100, 1) : 0; @endphp
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-900">{{ $detail->candidate->name ?? 'Unknown' }}</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($detail->votes) }} <span class="text-gray-400 font-normal">({{ $pct }}%)</span></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                        <div class="h-3 rounded-full bg-green-600 transition-all" style="width: {{ min($pct, 100) }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 pt-4 border-t border-gray-100 grid grid-cols-3 gap-4 text-center">
            <div class="p-3 bg-gray-50 rounded-xl">
                <p class="text-lg font-bold text-gray-900">{{ number_format($submission->spoilt_votes) }}</p>
                <p class="text-xs text-gray-500">Spoilt</p>
            </div>
            <div class="p-3 bg-gray-50 rounded-xl">
                <p class="text-lg font-bold text-gray-900">{{ number_format($submission->total_votes_cast) }}</p>
                <p class="text-xs text-gray-500">Total Cast</p>
            </div>
            <div class="p-3 bg-gray-50 rounded-xl">
                @php $turnout = $submission->registered_voters > 0 ? round(($submission->total_votes_cast / $submission->registered_voters) * 100, 1) : 0; @endphp
                <p class="text-lg font-bold {{ $turnout >= 70 ? 'text-green-600' : 'text-amber-600' }}">{{ $turnout }}%</p>
                <p class="text-xs text-gray-500">Turnout</p>
            </div>
        </div>
    </div>

    {{-- Admin Verification --}}
    @if(auth()->user()->isAdmin() && $submission->status === 'pending')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Admin Verification</h3>
        <div class="space-y-3">
            <textarea name="notes" rows="2" placeholder="Add verification notes (optional)" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500"></textarea>
            <div class="flex gap-3">
                <form method="POST" action="{{ route('votes.verify', $submission) }}">
                    @csrf
                    <input type="hidden" name="status" value="verified">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Verify
                    </button>
                </form>
                <form method="POST" action="{{ route('votes.verify', $submission) }}">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reject
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Audit Info --}}
    <div class="bg-gray-50 rounded-2xl p-5 text-xs text-gray-500 space-y-1">
        <p>IP Address: {{ $submission->ip_address ?? 'N/A' }}</p>
        <p>Recorded by: {{ $submission->user->name ?? 'N/A' }}</p>
        @if($submission->verified_at)
        <p>Verified by: {{ $submission->verifier->name ?? 'N/A' }} at {{ $submission->verified_at->format('d M Y H:i') }}</p>
        @endif
        @if($submission->notes)
        <p>Notes: {{ $submission->notes }}</p>
        @endif
    </div>
</div>
@endsection
