<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class agencies extends Model
{
    protected $table = 'agencies';

    protected $fillable = [
        'user_id',
        'name',
        'logo_path',
        'brand_color',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(clients::class, 'agency_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(projects::class, 'agency_id');
    }
}
