<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Complaint extends Model
{
    use HasFactory;

    // ---------------------------------------------------------------
    // Status constants - the single source of truth for valid statuses.
    // Used for validation, dropdowns, and badge colors.
    // ---------------------------------------------------------------
    public const STATUS_PENDING = 'pending';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_REJECTED = 'rejected';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_UNDER_REVIEW => 'Under Review',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_RESOLVED => 'Resolved',
        self::STATUS_REJECTED => 'Rejected',
    ];

    // ---------------------------------------------------------------
    // Category constants - matches the categories requested in the spec.
    // ---------------------------------------------------------------
    public const CATEGORIES = [
        'road' => 'Road / Potholes',
        'garbage' => 'Garbage Collection',
        'drainage' => 'Drainage / Flooding',
        'street_light' => 'Broken Street Light',
        'safety' => 'Public Safety',
        'others' => 'Others',
    ];

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

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'resolved_at' => 'datetime',
        ];
    }

    /**
     * Automatically generate a unique, human-friendly reference number
     * whenever a complaint is created, unless one was already provided.
     */
    protected static function booted(): void
    {
        static::creating(function (Complaint $complaint) {
            if (empty($complaint->reference_number)) {
                $complaint->reference_number = self::generateReferenceNumber();
            }
        });
    }

    public static function generateReferenceNumber(): string
    {
        do {
            $reference = 'ECS-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));
        } while (self::where('reference_number', $reference)->exists());

        return $reference;
    }

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ComplaintImage::class);
    }

    /**
     * Full audit trail of status changes, most recent first.
     */
    public function updates(): HasMany
    {
        return $this->hasMany(ComplaintUpdate::class)->latest();
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // ---------------------------------------------------------------
    // Accessors - small helpers so Blade views stay clean.
    // ---------------------------------------------------------------

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    /**
     * Bootstrap badge class to use for the current status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-secondary',
            self::STATUS_UNDER_REVIEW => 'bg-info text-dark',
            self::STATUS_IN_PROGRESS => 'bg-primary',
            self::STATUS_RESOLVED => 'bg-success',
            self::STATUS_REJECTED => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Bootstrap Icons class to use for the current category.
     */
    public function getCategoryIconAttribute(): string
    {
        return match ($this->category) {
            'road' => 'bi-cone-striped',
            'garbage' => 'bi-trash3',
            'drainage' => 'bi-water',
            'street_light' => 'bi-lightbulb',
            'safety' => 'bi-shield-exclamation',
            default => 'bi-three-dots',
        };
    }

    public function getHasLocationAttribute(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function getMapUrlAttribute(): ?string
    {
        return $this->has_location
            ? "https://www.google.com/maps?q={$this->latitude},{$this->longitude}"
            : null;
    }

    public function getResolutionPhotoUrlAttribute(): ?string
    {
        return $this->resolution_photo ? Storage::url($this->resolution_photo) : null;
    }
}
