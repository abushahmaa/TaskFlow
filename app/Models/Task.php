<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Task extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'project_id',
        'assigned_to',
        'created_by',
        'name',
        'description',
        'priority',
        'status',
        'deadline',
        'estimated_hours',
        'overdue_notified',
    ];

    protected $casts = [
        'priority'          => TaskPriority::class,
        'status'            => TaskStatus::class,
        'deadline'          => 'datetime',
        'estimated_hours'   => 'float',
        'overdue_notified'  => 'boolean',
    ];

    // ── Activity Log ──────────────────────────────────────────
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('task');
    }

    // ── Relations ─────────────────────────────────────────────
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class);
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
            ->whereNotIn('status', ['completed']);
    }

    public function scopeDueSoon($query, int $hours = 48)
    {
        return $query->whereBetween('deadline', [now(), now()->addHours($hours)])
            ->whereNotIn('status', ['completed']);
    }

    public function scopeForProject($query, int $projectId)
    {
        return $query->where('project_id', $projectId);
    }
}
