<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_log_id',
        'user_id',
        'message',
    ];

    // ── Relations ─────────────────────────────────────────────
    public function workLog(): BelongsTo
    {
        return $this->belongsTo(WorkLog::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
