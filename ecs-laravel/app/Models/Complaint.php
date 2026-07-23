<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Complaint extends Model
{
    use HasFactory;

    // All the statuses the system supports, used for dropdowns & validation.
    public const STATUSES = ['Pending', 'Under Review', 'In Progress', 'Resolved', 'Rejected'];

    public const CATEGORIES = ['Road', 'Garbage', 'Drainage', 'Street Light', 'Safety', 'Others'];

    protected $fillable = [
        'reference_number',
        'user_id',
        'title',
        'description',
        'category',
        'location',
        'latitude',
        'longitude',
        'status',
        'admin_remarks',
        'assigned_to',
        'department_id',
        'resolution_photo',
        'resolved_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'resolved_at' => 'datetime',
    ];

    /**
     * Generate a unique, human-friendly reference number such as ECS-2026-000123.
     * Used when a resident submits a new complaint.
     */
    public static function generateReferenceNumber(): string
    {
        do {
            $candidate = 'ECS-' . date('Y') . '-' . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('reference_number', $candidate)->exists());

        return $candidate;
    }

    // -------------------- Relationships --------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ComplaintImage::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(ComplaintUpdate::class)->latest();
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // -------------------- Helpers --------------------

    /**
     * Bootstrap badge color for a given status, used across the blade views.
     */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'Pending' => 'bg-secondary',
            'Under Review' => 'bg-info text-dark',
            'In Progress' => 'bg-warning text-dark',
            'Resolved' => 'bg-success',
            'Rejected' => 'bg-danger',
            default => 'bg-light text-dark',
        };
    }
}
