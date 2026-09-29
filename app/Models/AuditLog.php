<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * Disable updated_at timestamp.
     */
    public const UPDATED_AT = null;

    /**
     * Module constants
     */
    public const MODULE_AUTH = 'AUTH';

    public const MODULE_USERS = 'USERS';

    public const MODULE_ROLES = 'ROLES';

    public const MODULE_PROGRAMS = 'PROGRAMS';

    public const MODULE_DOCUMENTS = 'DOCUMENTS';

    public const MODULE_TASKS = 'TASKS';

    public const MODULE_APPROVALS = 'APPROVALS';

    public const MODULE_SERVICES = 'SERVICES';

    public const MODULE_MONEV = 'MONEV';

    public const MODULE_IMPACT = 'IMPACT';

    public const MODULE_SETTINGS = 'SETTINGS';

    /**
     * Action constants
     */
    public const ACTION_LOGIN = 'LOGIN';

    public const ACTION_LOGOUT = 'LOGOUT';

    public const ACTION_CREATE = 'CREATE';

    public const ACTION_UPDATE = 'UPDATE';

    public const ACTION_DELETE = 'DELETE';

    public const ACTION_SUBMIT = 'SUBMIT';

    public const ACTION_APPROVE = 'APPROVE';

    public const ACTION_REJECT = 'REJECT';

    public const ACTION_UPLOAD = 'UPLOAD';

    public const ACTION_DOWNLOAD = 'DOWNLOAD';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'module',
        'entity_type',
        'entity_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the user associated with the audit log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
