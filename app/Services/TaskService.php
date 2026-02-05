<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    /**
     * Get paginated tasks with filters.
     */
    public function getPaginatedTasks(
        User $user,
        ?string $status = null,
        ?string $priority = null,
        ?int $categoryId = null,
        ?string $search = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        return Task::query()
            ->with(['category'])
            ->forUser($user->id)
            ->byStatus($status)
            ->byPriority($priority)
            ->byCategory($categoryId)
            ->search($search)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get all tasks for a user.
     */
    public function getAllTasks(User $user): Collection
    {
        return Task::query()
            ->with(['category'])
            ->forUser($user->id)
            ->latest()
            ->get();
    }

    /**
     * Create a new task.
     */
    public function createTask(User $user, array $data): Task
    {
        $data['user_id'] = $user->id;

        return Task::create($data);
    }

    /**
     * Update an existing task.
     */
    public function updateTask(Task $task, array $data): Task
    {
        // Gestion de la date de complétion
        if (isset($data['status'])) {
            if ($data['status'] === 'completed' && $task->status !== 'completed') {
                $data['completed_at'] = now();
            } elseif ($data['status'] !== 'completed' && $task->status === 'completed') {
                $data['completed_at'] = null;
            }
        }

        $task->update($data);

        return $task->fresh(['category']);
    }

    /**
     * Delete a task.
     */
    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * Toggle task status between pending and completed.
     */
    public function toggleTaskStatus(Task $task): Task
    {
        if ($task->status === 'completed') {
            $task->status = 'pending';
        } else {
            $task->status = 'completed';
            $task->completed_at = now();
        }

        $task->save();

        return $task->fresh(['category']);
    }

    /**
     * Get task statistics for a user.
     */
    public function getTaskStatistic(User $user): array
    {
        $tasks = Task::forUser($user->id)->get();

        return [
            'total' => $tasks->count(),
            'pending' => $tasks->where('status', 'pending')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
            'overdue' => $tasks->filter(fn ($task) => $task->isOverdue())->count(),
            'high_priority' => $tasks->where('priority', 'high')->count(),
        ];
    }

    /**
     * Get overdue tasks for a user.
     */
    public function getOverdueTasks(User $user): Collection
    {
        return Task::query()
            ->with(['category'])
            ->forUser($user->id)
            ->overdue()
            ->get();
    }

    /**
     * Get tasks due today for a user.
     */
    public function getTasksDueToday(User $user): Collection
    {
        return Task::query()
            ->with(['category'])
            ->forUser($user->id)
            ->whereDate('due_date', Carbon::today())
            ->whereIn('status', ['pending', 'in_progress'])
            ->get();
    }
}
