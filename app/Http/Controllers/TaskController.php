<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
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
            perPage: 15
        );

        $categories = Auth::user()->categories()->get();
        $stats = $this->taskService->getTaskStatistic($request->user());

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'categories' => $categories,
            'filters' => $filters,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $categories = Auth::user()->categories()->get();

        return Inertia::render('Tasks/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $task = $this->taskService->createTask(
            $request->user(),
            $request->validated()
        );

        return redirect()->route('tasks.index')
            ->with('success', 'Tâche créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): Response
    {
        // Vérification de l'autorisation via TaskPolicy
        $this->authorize('view', $task);
        $task->load(['category', 'user']);

        return Inertia::render('Tasks/Show', [
            'task' => $task,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): Response
    {
        // Vérification de l'autorisation via TaskPolicy
        $this->authorize('update', $task);
        $categories = Auth::user()->categories()->get();
        $task->load('category');

        return Inertia::render('Tasks/Edit', [
            'task' => $task,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        // Vérification de l'autorisation via TaskPolicy
        $this->authorize('update', $task);
        $this->taskService->updateTask($task, $request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Tâche mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
        // Vérification de l'autorisation via TaskPolicy
        $this->authorize('delete', $task);
        $this->taskService->deleteTask($task);

        return redirect()->route('tasks.index')
            ->with('success', 'Tâche supprimée avec succès.');
    }

    /**
     * Toggle task completion status.
     */
    public function toggleStatus(Task $task): RedirectResponse
    {

        $this->taskService->toggleTaskStatus($task);

        return back()->with('success', 'Statut de la tâche mis à jour.');
    }
}
