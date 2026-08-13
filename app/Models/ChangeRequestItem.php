<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangeRequestItem extends Model
{
    protected $fillable = [
        'approval_id',
        'description',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function approval(): BelongsTo
    {
        return $this->belongsTo(approvales::class, 'approval_id');
    }

    public function deliverable()
    {
        return $this->hasOneThrough(
            deliverables::class,
            approvales::class,
            'id',
            'id',
            'approval_id',
            'deliverable_id'
        );
    }
}
