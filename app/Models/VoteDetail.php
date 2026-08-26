<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoteDetail extends Model
{
    protected $fillable = ['vote_submission_id', 'candidate_id', 'votes'];

    protected function casts(): array
    {
        return ['votes' => 'integer'];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(VoteSubmission::class, 'vote_submission_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
