@extends('layouts.app')
@section('title', 'Submit Report')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="voteForm()" x-init="init()">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Submit Polling Station Report</h1>
            <p class="text-sm text-gray-500">Fill in all required fields accurately</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
        <div class="flex items-center gap-2 text-red-700 font-medium text-sm mb-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Please fix the following errors:
        </div>
        <ul class="text-sm text-red-600 space-y-1 ml-7">
            @foreach($errors->all() as $error)
            <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('votes.store') }}" @submit="loading = true">
        @csrf

        {{-- Election Type --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-700 text-sm font-bold">1</span>
                Election Type
            </h2>
            <select name="election_type_id" x-model="electionTypeId" @change="loadCandidates()" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-gray-50 focus:bg-white">
                <option value="">— Select Election Type —</option>
                @foreach($electionTypes as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Polling Station --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-700 text-sm font-bold">2</span>
                Polling Station
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Search Station</label>
                    <input type="text" x-model="stationSearch" @input="filterStations()" placeholder="Type to search polling station..."
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Select Station</label>
                    <select name="polling_station_id" x-model="stationId" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-gray-50 focus:bg-white">
                        <option value="">— Select Polling Station —</option>
                        <template x-for="s in filteredStations" :key="s.id">
                            <option :value="s.id" x-text="s.name + ' (Ward: ' + s.ward_name + ')'"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>

        {{-- Agent Details --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-700 text-sm font-bold">3</span>
                Agent Details
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Your Name</label>
                    <input type="text" name="agent_name" value="{{ auth()->user()->name }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Agent Code</label>
                    <input type="text" name="agent_code" value="E{{ str_pad(auth()->user()->id, 7, '0', STR_PAD_LEFT) }}-{{ auth()->user()->phone ?? '0700000000' }}-{{ str_pad(auth()->user()->id, 5, '0', STR_PAD_LEFT) }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-gray-50 focus:bg-white">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Presiding Officer</label>
                    <input type="text" name="presiding_officer" placeholder="Name of presiding officer"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-gray-50 focus:bg-white">
                </div>
            </div>
        </div>

        {{-- Candidate Votes --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-700 text-sm font-bold">4</span>
                Candidate Votes
            </h2>
            <div class="space-y-3">
                <template x-for="(cv, idx) in candidateVotes" :key="idx">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="flex-1">
                            <input type="hidden" :name="'candidate_votes[' + idx + '][candidate_id]'" :value="cv.candidate_id">
                            <span class="text-sm font-medium text-gray-900" x-text="cv.name"></span>
                        </div>
                        <input type="number" :name="'candidate_votes[' + idx + '][votes]'" x-model="cv.votes" min="0" placeholder="0"
                               class="w-28 px-3 py-2 border border-gray-200 rounded-lg text-sm text-right focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white">
                    </div>
                </template>
                @if(!count(auth()->user()->submissions))
                <p class="text-xs text-gray-400 italic">Select election type and station first to load candidates.</p>
                @endif
            </div>
        </div>

        {{-- Vote Summary --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-700 text-sm font-bold">5</span>
                Vote Summary
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Spoilt Votes</label>
                    <input type="number" name="spoilt_votes" x-model="spoiltVotes" min="0" value="0" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Total Votes Cast</label>
                    <input type="number" name="total_votes_cast" x-model="totalVotesCast" min="0" value="0" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Registered Voters</label>
                    <input type="number" name="registered_voters" x-model="registeredVoters" min="0" value="0" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-gray-50 focus:bg-white">
                </div>
            </div>

            {{-- Auto-calc validation --}}
            <div class="mt-4 p-3 rounded-xl" :class="voteSumOk ? 'bg-green-50 border border-green-200' : 'bg-amber-50 border border-amber-200'">
                <div class="flex items-center gap-2 text-sm">
                    <span x-show="voteSumOk" class="text-green-700 font-medium">
                        <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Vote counts match: <span x-text="candidateSum"></span> + <span x-text="spoiltVotes"></span> = <span x-text="totalVotesCast"></span>
                    </span>
                    <span x-show="!voteSumOk" class="text-amber-700 font-medium">
                        <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        Mismatch: <span x-text="candidateSum"></span> + <span x-text="spoiltVotes"></span> = <span x-text="parseInt(candidateSum) + parseInt(spoiltVotes || 0)"></span> ≠ <span x-text="totalVotesCast"></span>
                    </span>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-4">
            <button type="submit"
                    class="bg-green-700 hover:bg-green-800 text-white font-semibold py-3 px-8 rounded-xl transition-all shadow-lg shadow-green-700/25 hover:shadow-green-700/40 active:scale-[0.98] flex items-center gap-2"
                    :disabled="loading">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                <span x-show="!loading">Submit Report</span>
                <span x-show="loading">Submitting...</span>
            </button>
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium">Cancel</a>
        </div>
    </form>

    {{-- Bulk Paste Section --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-8">
        <h2 class="text-lg font-bold text-gray-900 mb-2">Bulk Paste (WhatsApp)</h2>
        <p class="text-sm text-gray-500 mb-4">Paste multiple lines — one per station. Format: Constituency, Ward, Station, AgentName, AgentCode, PO, Cand1:Votes, ..., Spoilt, TotalCast, Registered</p>
        <form method="POST" action="{{ route('votes.bulk') }}">
            @csrf
            <input type="hidden" name="election_type_id" :value="electionTypeId">
            <textarea name="bulk_data" rows="8" placeholder="Lurambi, Sheywe, Bondeni Primary School, Alice M., E0987654-0701092708-74225, James K., Candidate A:120, Candidate B:85, Spoilt:7, Total:257, Registered:400"
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-gray-50 focus:bg-white"></textarea>
            <button type="submit" class="mt-3 bg-gray-800 hover:bg-gray-900 text-white font-medium py-2.5 px-6 rounded-xl text-sm transition-all">
                Process Bulk Data
            </button>
        </form>

        @if(session('bulk_results'))
        <div class="mt-4 p-4 bg-gray-50 rounded-xl">
            <p class="text-sm font-semibold {{ session('bulk_results.errors') > 0 ? 'text-amber-700' : 'text-green-700' }}">
                ✅ {{ session('bulk_results.success') }} successful, ❌ {{ session('bulk_results.errors') }} errors
            </p>
            @foreach(session('bulk_results.details') as $detail)
            <p class="text-xs mt-1 {{ $detail['success'] ? 'text-green-600' : 'text-red-600' }}">
                Line {{ $detail['line'] }}: {{ $detail['message'] }} {{ $detail['station'] ?? '' }}
            </p>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function voteForm() {
    return {
        loading: false,
        electionTypeId: '',
        stationId: '',
        stationSearch: '',
        stations: [],
        filteredStations: [],
        candidateVotes: [],
        spoiltVotes: 0,
        totalVotesCast: 0,
        registeredVoters: 0,

        get candidateSum() {
            return this.candidateVotes.reduce((s, c) => s + (parseInt(c.votes) || 0), 0);
        },
        get voteSumOk() {
            return this.totalVotesCast > 0 && (this.candidateSum + parseInt(this.spoiltVotes || 0)) === parseInt(this.totalVotesCast);
        },

        async init() {
            const resp = await fetch('/api/stations/all');
            if (resp.ok) {
                this.stations = await resp.json();
                this.filteredStations = this.stations.slice(0, 50);
            }
        },

        filterStations() {
            const q = this.stationSearch.toLowerCase();
            if (!q) {
                this.filteredStations = this.stations.slice(0, 50);
                return;
            }
            this.filteredStations = this.stations.filter(s =>
                s.name.toLowerCase().includes(q) || s.ward_name.toLowerCase().includes(q)
            ).slice(0, 50);
        },

        async loadCandidates() {
            if (!this.electionTypeId) return;
            const resp = await fetch('/api/candidates/' + this.electionTypeId);
            if (resp.ok) {
                const cands = await resp.json();
                this.candidateVotes = cands.map(c => ({
                    candidate_id: c.id,
                    name: c.name,
                    votes: 0
                }));
            }
        }
    };
}
</script>
@endpush
