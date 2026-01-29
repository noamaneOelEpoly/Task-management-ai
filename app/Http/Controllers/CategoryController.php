<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = auth()->user()->categories()->withCount('tasks')->paginate(12);
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
        ]);

        auth()->user()->categories()->create($validated);

        return redirect()->route('categories.index')->with('success', 'Catégorie créée avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): View
    {
        if ($category->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette catégorie.');
        }
        $tasks = $category->tasks()->paginate(10);
        return view('categories.show', compact('category', 'tasks'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        if ($category->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à éditer cette catégorie.');
        }
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        if ($category->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à mettre à jour cette catégorie.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
        ]);

        $category->update($validated);

        return redirect()->route('categories.show', $category)->with('success', 'Catégorie mise à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Check if user owns this category
        if ($category->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cette catégorie.');
        }
        
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée avec succès!');
    }
}
