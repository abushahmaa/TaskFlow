<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'description',
        'hours_worked',
        'attachment_path',
        'attachment_name',
    ];

    protected $casts = [
        'hours_worked' => 'float',
    ];

    // ── Relations ─────────────────────────────────────────────
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(LogReply::class);
    }

    // ── Helpers ───────────────────────────────────────────────
    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }
}
