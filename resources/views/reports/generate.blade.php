@extends('layouts.app')
@section('title', 'Report Generated')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('reports.index') }}" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Report: {{ ucfirst($type) }}</h1>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" x-data="{ copied: false }">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">Report Output</h3>
            <div class="flex gap-2">
                <button @click="navigator.clipboard.writeText(document.getElementById('reportOutput').textContent); copied = true; setTimeout(() => copied = false, 2000);"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-sm font-medium transition-colors">
                    <span x-text="copied ? '✓ Copied!' : '📋 Copy'"></span>
                </button>
                <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-xl text-sm font-medium transition-colors">New Report</a>
            </div>
        </div>
        <pre id="reportOutput" class="p-6 text-sm font-mono text-gray-700 whitespace-pre-wrap leading-relaxed bg-gray-50">{{ $report }}</pre>
    </div>
</div>
@endsection
