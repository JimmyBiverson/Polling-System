<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class County extends Model
{
    protected $fillable = ['name', 'code'];

    public function constituencies(): HasMany
    {
        return $this->hasMany(Constituency::class);
    }
}
