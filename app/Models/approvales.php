<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class approvales extends Model
{
    protected $fillable = [
        'deliverable_id',
        'client_id',
        'status',
        'comments',
        'created_at',
        'updated_at',
    ];

    public function deliverable(): BelongsTo
    {
        return $this->belongsTo(deliverables::class, 'deliverable_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(clients::class, 'client_id');
    }
}
