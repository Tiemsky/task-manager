<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Request $request): Response
    {
       // Vérification de l'autorisation via CategoryPolicy
        $this->authorize('viewAny', Category::class);
        $categories = $request->user()->categories()->withCount('tasks')->get();

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        $request->user()->categories()->create($request->only('name', 'color'));

        return redirect()->back()->with('success', 'Catégorie créée.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
      // Vérification de l'autorisation via CategoryPolicy
        $this->authorize('update', $category);
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        $category->update($request->only('name', 'color'));

        return redirect()->back()->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Vérification de l'autorisation via CategoryPolicy
        $this->authorize('delete', $category);
        $category->delete();

        return redirect()->back()->with('success', 'Catégorie supprimée.');
    }
}
