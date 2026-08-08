<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class deliverables extends Model
{
    protected $fillable = [
        'project_id',
        'uploaded_by',
        'title',
        'file_path',
        'created_at',
        'updated_at',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(projects::class, 'project_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(approvales::class, 'deliverable_id');
    }
}
