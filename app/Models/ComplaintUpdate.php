<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintUpdate extends Model
{
    protected $fillable = [
        'complaint_id',
        'user_id',
        'status',
        'remarks',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    /**
     * The admin who made this update. Null for system-generated entries
     * (e.g. the "complaint submitted" entry created at submission time).
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return Complaint::STATUSES[$this->status] ?? $this->status;
    }
}
