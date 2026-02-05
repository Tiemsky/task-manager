<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Task completed()
 * @method static \Database\Factories\TaskFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Task inProgress()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task pending()
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 *
 * @mixin \Eloquent
 */
class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'due_date',
        'status',
        'priority',
        'completed_at',
    ];

    // cast due_date and completed_at to Carbon instances
    protected $dates = [
        'due_date',
        'completed_at',
    ];

    /**
     * Get the user that owns the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the task.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope a query to only include pending tasks.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include in-progress tasks.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include completed tasks.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Scope a query to only include tasks for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }

        return $query;
    }

    /**
     * Scope a query to filter by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        if ($priority) {
            return $query->where('priority', $priority);
        }

        return $query;
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        if ($categoryId) {
            return $query->where('category_id', $categoryId);
        }

        return $query;
    }

    /**
     * Scope a query to get overdue tasks.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::today())
            ->whereIn('status', ['pending', 'in_progress']);
    }

    /**
     * Check if task is overdue.
     */
    public function isOverdue(): bool
    {
        // Ensure due_date is a Carbon instance before calling isPast()
        if ($this->due_date && ! ($this->due_date instanceof Carbon)) {
            $this->due_date = Carbon::parse($this->due_date);
        }

        return $this->due_date &&
               $this->due_date->isPast() &&
               in_array($this->status, ['pending', 'in_progress']);
    }

    /**
     * Mark task as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark task as pending.
     */
    public function markAsPending(): void
    {
        $this->update([
            'status' => 'pending',
            'completed_at' => null,
        ]);
    }
}
