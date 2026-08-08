<?php

namespace App\Models;
use App\Models\Message;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class projects extends Model
{
    protected $fillable = [
        'agency_id',
        'client_id',
        'name',
        'description',
        'status',
        'created_at',
        'updated_at',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(agencies::class, 'agency_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(clients::class, 'client_id');
    }

    public function deliverables(): HasMany
    {
        return $this->hasMany(deliverables::class, 'project_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'project_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'project_id');
    }
}
