<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Program status constants
     */
    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_SUBMITTED = 'SUBMITTED';

    public const STATUS_UNDER_REVIEW = 'UNDER_REVIEW';

    public const STATUS_APPROVED = 'APPROVED';

    public const STATUS_REJECTED = 'REJECTED';

    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';

    public const STATUS_COMPLETED = 'COMPLETED';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'pic_id',
        'pic_name',
        'start_date',
        'end_date',
        'budget',
        'progress',
        'status',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'budget' => 'decimal:2',
            'progress' => 'integer',
        ];
    }

    /**
     * Get creator of the program.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get Person in Charge (PIC).
     */
    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    /**
     * Get members involved in the program.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'program_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get documents attached to the program.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get tasks related to the program.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get approvals history for the program.
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class);
    }

    /**
     * Get the latest approval record.
     */
    public function latestApproval(): HasOne
    {
        return $this->hasOne(Approval::class)->latestOfMany();
    }

    /**
     * Get evaluations / monev records for the program.
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(ProgramEvaluation::class);
    }

    /**
     * Get the latest evaluation record.
     */
    public function latestEvaluation(): HasOne
    {
        return $this->hasOne(ProgramEvaluation::class)->latestOfMany();
    }

    /**
     * Helper to get effective PIC display name.
     */
    public function getPicDisplayNameAttribute(): string
    {
        return $this->pic?->name ?? $this->pic_name ?? '-';
    }
}
