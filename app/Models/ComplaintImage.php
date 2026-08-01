<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ComplaintImage extends Model
{
    protected $fillable = [
        'complaint_id',
        'image_path',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    /**
     * Public, browser-usable URL for this image (requires `php artisan storage:link`).
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->image_path);
    }
}
