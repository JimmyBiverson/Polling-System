@extends('layouts.app')
@section('title', 'Reports')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Report Generator</h1>
        <p class="text-sm text-gray-500 mt-1">Generate tallied reports for constituencies, wards, or individual stations.</p>
    </div>

    {{-- Report Form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('reports.generate') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Report Type</label>
                    <select name="type" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 bg-gray-50 focus:bg-white">
                        <option value="constituency">Constituency</option>
                        <option value="ward">Ward</option>
                        <option value="station">Polling Station</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Search</label>
                    <input type="text" name="identifier" required placeholder="Type name or ID..."
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 bg-gray-50 focus:bg-white">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-3 px-4 rounded-xl transition-all">
                        Generate Report
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
        {{ session('error') }}
    </div>
    @endif

    {{-- Report Output --}}
    @if(isset($report))
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" x-data="{ copied: false }">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">{{ ucfirst($type) }} Report</h3>
            <button @click="
                    navigator.clipboard.writeText(document.getElementById('reportText').textContent);
                    copied = true;
                    setTimeout(() => copied = false, 2000);
                    "
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-sm font-medium transition-colors">
                <svg x-show="!copied" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <svg x-show="copied" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span x-text="copied ? 'Copied!' : 'Copy'"></span>
            </button>
        </div>
        <pre id="reportText" class="p-6 text-sm font-mono text-gray-700 whitespace-pre-wrap leading-relaxed bg-gray-50">{{ $report }}</pre>
    </div>
    @endif

    {{-- Export --}}
    @if(auth()->user()->isAdmin())
    <a href="{{ route('reports.export') }}" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white font-medium py-2.5 px-6 rounded-xl text-sm transition-all">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download CSV Export
    </a>
    @endif
</div>
@endsection
