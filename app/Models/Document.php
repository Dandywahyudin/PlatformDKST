<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Document category constants
     */
    public const CATEGORY_PROPOSAL = 'PROPOSAL';

    public const CATEGORY_TOR = 'TOR';

    public const CATEGORY_BUDGET = 'BUDGET';

    public const CATEGORY_APPROVAL_LETTER = 'APPROVAL_LETTER';

    public const CATEGORY_REPORT = 'REPORT';

    public const CATEGORY_OTHER = 'OTHER';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'program_id',
        'uploaded_by',
        'name',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'category',
        'description',
    ];

    /**
     * Get the program associated with the document.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the user who uploaded the document.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get human-readable file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.($units[$i] ?? 'B');
    }
}
