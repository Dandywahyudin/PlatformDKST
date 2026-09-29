<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramEvaluation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Period constants
     */
    public const PERIOD_Q1 = 'TRIWULAN_1';

    public const PERIOD_Q2 = 'TRIWULAN_2';

    public const PERIOD_Q3 = 'TRIWULAN_3';

    public const PERIOD_Q4 = 'TRIWULAN_4';

    public const PERIOD_MIDTERM = 'MIDTERM';

    public const PERIOD_FINAL = 'FINAL';

    public const PERIOD_MONTHLY = 'MONTHLY';

    /**
     * Status constants
     */
    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_SUBMITTED = 'SUBMITTED';

    public const STATUS_REVIEWED = 'REVIEWED';

    public const STATUS_APPROVED = 'APPROVED';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'program_id',
        'evaluator_id',
        'evaluation_period',
        'evaluation_date',
        'progress_percentage',
        'budget_realization',
        'achievements',
        'obstacles',
        'recommendations',
        'score',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'evaluation_date' => 'date',
            'progress_percentage' => 'integer',
            'budget_realization' => 'decimal:2',
            'score' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Program being evaluated.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    /**
     * Evaluator / Reviewer user.
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    /**
     * Period label helper.
     */
    public function getPeriodLabelAttribute(): string
    {
        return match ($this->evaluation_period) {
            self::PERIOD_Q1 => 'Triwulan I (Q1)',
            self::PERIOD_Q2 => 'Triwulan II (Q2)',
            self::PERIOD_Q3 => 'Triwulan III (Q3)',
            self::PERIOD_Q4 => 'Triwulan IV (Q4)',
            self::PERIOD_MIDTERM => 'Evaluasi Paruh Waktu (Mid-Term)',
            self::PERIOD_FINAL => 'Evaluasi Akhir (Final)',
            self::PERIOD_MONTHLY => 'Monitoring Bulanan',
            default => $this->evaluation_period,
        };
    }

    /**
     * Status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'bg-slate-100 text-slate-700 border-slate-200',
            self::STATUS_SUBMITTED => 'bg-blue-50 text-blue-700 border-blue-200',
            self::STATUS_REVIEWED => 'bg-amber-50 text-amber-700 border-amber-200',
            self::STATUS_APPROVED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }
}
