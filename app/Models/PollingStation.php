<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PollingStation extends Model
{
    protected $fillable = [
        'ward_id', 'name', 'code', 'presiding_officer', 'registered_voters',
    ];

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(VoteSubmission::class);
    }

    public function latestSubmission(): HasOne
    {
        return $this->hasOne(VoteSubmission::class)->latestOfMany('submitted_at');
    }

    public function getLatestSubmission()
    {
        return $this->submissions()->latest()->first();
    }

    public function constituency()
    {
        return $this->ward?->constituency;
    }

    public function county()
    {
        return $this->ward?->constituency?->county;
    }
}
