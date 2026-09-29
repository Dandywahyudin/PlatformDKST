<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultationService extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Service type constants
     */
    public const TYPE_VALUASI_TEKNOLOGI = 'VALUASI_TEKNOLOGI';

    public const TYPE_FASILITASI_HKI = 'FASILITASI_HKI';

    public const TYPE_INKUBASI_STARTUP = 'INKUBASI_STARTUP';

    public const TYPE_HILIRISASI_INDUSTRI = 'HILIRISASI_INDUSTRI';

    public const TYPE_LEGALITAS_KONTRAK = 'LEGALITAS_KONTRAK';

    public const TYPE_LAINNYA = 'LAINNYA';

    /**
     * Status constants
     */
    public const STATUS_PENDING = 'PENDING';

    public const STATUS_IN_REVIEW = 'IN_REVIEW';

    public const STATUS_SCHEDULED = 'SCHEDULED';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_REJECTED = 'REJECTED';

    public const STATUS_CANCELLED = 'CANCELLED';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_number',
        'service_type',
        'title',
        'description',
        'applicant_id',
        'institution',
        'phone',
        'consultant_id',
        'scheduled_at',
        'meeting_link_or_location',
        'status',
        'consultation_notes',
        'action_plan',
        'rejection_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the applicant user.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    /**
     * Get the assigned consultant.
     */
    public function consultant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    /**
     * Readable service type label.
     */
    public function getServiceTypeLabelAttribute(): string
    {
        return match ($this->service_type) {
            self::TYPE_VALUASI_TEKNOLOGI => 'Valuasi Teknologi & Paten',
            self::TYPE_FASILITASI_HKI => 'Fasilitasi Pendaftaran HKI / Paten',
            self::TYPE_INKUBASI_STARTUP => 'Pendampingan & Inkubasi Startup',
            self::TYPE_HILIRISASI_INDUSTRI => 'Konsultasi Hilirisasi Industri & Mitra',
            self::TYPE_LEGALITAS_KONTRAK => 'Penyusunan Kontrak Lisensi & Kerjasama',
            default => 'Layanan Konsultasi Lainnya',
        };
    }

    /**
     * Badge status color class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-amber-50 text-amber-700 border-amber-200',
            self::STATUS_IN_REVIEW => 'bg-blue-50 text-blue-700 border-blue-200',
            self::STATUS_SCHEDULED => 'bg-purple-50 text-purple-700 border-purple-200',
            self::STATUS_COMPLETED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::STATUS_REJECTED => 'bg-rose-50 text-rose-700 border-rose-200',
            self::STATUS_CANCELLED => 'bg-slate-100 text-slate-600 border-slate-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }
}
