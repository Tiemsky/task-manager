<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) { }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
         // Vérification de l'autorisation via TaskPolicy
        $this->authorize('viewAny', Task::class);
        $filters = $request->only(['status', 'priority', 'category_id', 'search']);

        $tasks = $this->taskService->getPaginatedTasks(
            user: $request->user(),
            status: $filters['status'] ?? null,
            priority: $filters['priority'] ?? null,
            categoryId: $filters['category_id'] ?? null,
            search: $filters['search'] ?? null,
            perPage: $request->input('per_page', 15)
        );

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->createTask(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'message' => 'Task created successfully',
            'data' => new TaskResource($task->load('category')),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): TaskResource
    {
         // Vérification de l'autorisation via TaskPolicy
        $this->authorize('view', $task);
        $task->load(['category', 'user']);

        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
      // Vérification de l'autorisation via TaskPolicy
        $this->authorize('update', $task);
        $task = $this->taskService->updateTask($task, $request->validated());

        return response()->json([
            'message' => 'Task updated successfully',
            'data' => new TaskResource($task),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        // Vérification de l'autorisation via TaskPolicy
        $this->authorize('delete', $task);
        $this->taskService->deleteTask($task);

        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }

    /**
     * Toggle task completion status.
     */
    public function toggleStatus(Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $task = $this->taskService->toggleTaskStatus($task);

        return response()->json([
            'message' => 'Task status updated successfully',
            'data' => new TaskResource($task),
        ]);
    }

    /**
     * Get task statistics.
     */
    public function stats(Request $request): JsonResponse
    {
        $stats = $this->taskService->getTaskStatistic($request->user());

        return response()->json([
            'data' => $stats,
        ]);
    }

    /**
     * Get overdue tasks.
     */
    public function overdue(Request $request): AnonymousResourceCollection
    {
        $tasks = $this->taskService->getOverdueTasks($request->user());

        return TaskResource::collection($tasks);
    }
}
