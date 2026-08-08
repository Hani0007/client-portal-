<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class clients extends Model
{
    protected $fillable = [
        'agency_id',
        'user_id',
        'name',
        'email',
        'company_name',
        'created_at',
        'updated_at',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(agencies::class, 'agency_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(projects::class, 'client_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(approvales::class, 'client_id');
    }
}
