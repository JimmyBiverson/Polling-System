<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VoteSubmission extends Model
{
    protected $fillable = [
        'polling_station_id', 'election_type_id', 'user_id',
        'agent_name', 'agent_code', 'presiding_officer',
        'spoilt_votes', 'total_votes_cast', 'registered_voters',
        'status', 'notes', 'ip_address', 'device_info',
        'submission_hash', 'submitted_at', 'verified_at', 'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'spoilt_votes' => 'integer',
            'total_votes_cast' => 'integer',
            'registered_voters' => 'integer',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function pollingStation(): BelongsTo
    {
        return $this->belongsTo(PollingStation::class);
    }

    public function electionType(): BelongsTo
    {
        return $this->belongsTo(ElectionType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(VoteDetail::class);
    }

    public function generateHash(): string
    {
        $data = json_encode([
            $this->polling_station_id,
            $this->election_type_id,
            $this->agent_code,
            $this->spoilt_votes,
            $this->total_votes_cast,
            $this->registered_voters,
            $this->submitted_at,
        ]);

        return hash('sha256', $data);
    }
}
