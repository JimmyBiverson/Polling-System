<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::get('/wards/{constituencyId}', [ApiController::class, 'wardsByConstituency']);
Route::get('/stations/{wardId}', [ApiController::class, 'stationsByWard']);
Route::get('/candidates/{electionTypeId}', [ApiController::class, 'candidatesByElection']);
Route::get('/live-stats', [ApiController::class, 'liveStats']);

// All stations with ward names for search
Route::get('/stations/all', function () {
    $stations = \App\Models\PollingStation::with('ward')->get()->map(function ($s) {
        return [
            'id' => $s->id,
            'name' => $s->name,
            'ward_name' => $s->ward->name ?? '',
            'registered_voters' => $s->registered_voters,
        ];
    });
    return response()->json($stations);
});
