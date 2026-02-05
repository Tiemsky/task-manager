<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['status', 'priority', 'category_id', 'search']);

        $tasks = $request->user()->tasks()
        /*
        * On loaded la catégorie pour éviter les requêtes N+1
        */
            ->with('category')
            /*
            * Si un filtre de catégorie est présent, on l'applique
            */
            ->search($request->search)
            ->latest()
            ->paginate(10);

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'categories' => $request->user()->categories,
            'filters' => $filters,
            'stats' => '',
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tasks/Create', [
            'categories' => auth()->user()->categories,
        ]);
    }

    public function show(Task $task): Response
    {
        return Inertia::render('Tasks/Show', [
            'task' => $task->load('category'),
            'categories' => auth()->user()->categories,
        ]);
    }

    public function edit(Task $task): Response
    {
        return Inertia::render('Tasks/Edit', [
            'task' => $task->load('category'),
            'categories' => auth()->user()->categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
        ]);

        $request->user()->tasks()->create($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Tâche créée avec succès !');

    }

    public function update(Request $request, Task $task)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,in_progress,completed',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Tâche mise à jour.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Tâche supprimée définitivement.');
    }

    public function toggleStatus(Task $task): RedirectResponse
    {

        if ($task->status === 'completed') {
            $task->status = 'pending';
            $task->completed_at = null;
            $task->save();
        } else {
            $task->status = 'completed';
            $task->completed_at = now();
            $task->save();
        }

        $task->fresh(['category']);

        return back()->with('success', 'Statut de la tâche mis à jour.');
    }
}
