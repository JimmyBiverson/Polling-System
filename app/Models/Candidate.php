<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{
    protected $fillable = ['election_type_id', 'name', 'party'];

    public function electionType(): BelongsTo
    {
        return $this->belongsTo(ElectionType::class);
    }
}
