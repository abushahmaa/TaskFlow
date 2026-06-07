<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'manager_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'status'     => ProjectStatus::class,
    ];

    // ── Activity Log ──────────────────────────────────────────
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('project');
    }

    // ── Relations ─────────────────────────────────────────────
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('manager_id', $userId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // ── Accessors ─────────────────────────────────────────────
    public function getCompletionPercentageAttribute(): float
    {
        $total = $this->tasks()->count();
        if ($total === 0) return 0;
        $completed = $this->tasks()->where('status', 'completed')->count();
        return round(($completed / $total) * 100, 1);
    }
}
