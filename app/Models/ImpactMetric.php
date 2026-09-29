<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImpactMetric extends Model
{
    use HasFactory;

    /**
     * Category constants
     */
    public const CAT_STARTUP_GROWTH = 'STARTUP_GROWTH';

    public const CAT_PATENT_HKI = 'PATENT_HKI';

    public const CAT_COMMERCIALIZATION = 'COMMERCIALIZATION';

    public const CAT_WORKFORCE = 'WORKFORCE';

    public const CAT_FUNDING_INVESTMENT = 'FUNDING_INVESTMENT';

    public const CAT_SOCIO_ECONOMIC = 'SOCIO_ECONOMIC';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'year',
        'category',
        'metric_name',
        'target_value',
        'realized_value',
        'unit',
        'description',
        'recorded_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'target_value' => 'decimal:2',
            'realized_value' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * User who recorded the metric.
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Calculate achievement percentage.
     */
    public function getAchievementPercentageAttribute(): float
    {
        if ($this->target_value <= 0) {
            return $this->realized_value > 0 ? 100.0 : 0.0;
        }

        return round(($this->realized_value / $this->target_value) * 100, 1);
    }

    /**
     * Category label helper.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            self::CAT_STARTUP_GROWTH => 'Pertumbuhan & Inkubasi Startup',
            self::CAT_PATENT_HKI => 'Paten & Kekayaan Intelektual',
            self::CAT_COMMERCIALIZATION => 'Komersialisasi & Royalti Riset',
            self::CAT_WORKFORCE => 'Penyerapan Tenaga Kerja',
            self::CAT_FUNDING_INVESTMENT => 'Pendanaan & Investasi Riset/Startup',
            self::CAT_SOCIO_ECONOMIC => 'Dampak Sosial & Ekonomi Mitra',
            default => $this->category,
        };
    }
}
