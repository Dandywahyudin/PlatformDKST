<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalAction extends Model
{
    use HasFactory;

    /**
     * Disable updated_at timestamp.
     */
    public const UPDATED_AT = null;

    /**
     * Action constants
     */
    public const ACTION_SUBMIT = 'SUBMIT';

    public const ACTION_APPROVE = 'APPROVE';

    public const ACTION_REJECT = 'REJECT';

    public const ACTION_COMMENT = 'COMMENT';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'approval_id',
        'user_id',
        'action',
        'comment',
    ];

    /**
     * Get associated approval.
     */
    public function approval(): BelongsTo
    {
        return $this->belongsTo(Approval::class);
    }

    /**
     * Get user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
