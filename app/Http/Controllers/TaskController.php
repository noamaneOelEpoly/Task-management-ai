<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tasks = auth()->user()->tasks()
            ->with('category')
            ->orderBy('due_date')
            ->paginate(10);
        
        $categories = auth()->user()->categories;
        $stats = [
            'total' => auth()->user()->tasks()->count(),
            'completed' => auth()->user()->tasks()->whereNotNull('completed_at')->count(),
            'overdue' => auth()->user()->tasks()
                ->whereNull('completed_at')
                ->where('due_date', '<', now())
                ->count(),
        ];

        return view('tasks.index', compact('tasks', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = auth()->user()->categories;
        return view('tasks.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date_format:Y-m-d\TH:i',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        auth()->user()->tasks()->create($validated);

        return redirect()->route('tasks.index')->with('success', 'Tâche créée avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): View
    {
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette tâche.');
        }
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): View
    {
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à éditer cette tâche.');
        }
        $categories = auth()->user()->categories;
        return view('tasks.edit', compact('task', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette tâche.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date_format:Y-m-d\TH:i',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.show', $task)->with('success', 'Tâche mise à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cette tâche.');
        }
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Tâche supprimée avec succès!');
    }

    /**
     * Toggle task completion status.
     */
    public function toggle(Task $task): RedirectResponse
    {
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette tâche.');
        }
        $task->toggleStatus();

        return back()->with('success', 'Statut de la tâche mis à jour!');
    }

    /**
     * Filter tasks by category.
     */
    public function filterByCategory(Category $category): View
    {
        if ($category->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette catégorie.');
        }
        
        $tasks = $category->tasks()
            ->orderBy('due_date')
            ->paginate(10);
        
        $categories = auth()->user()->categories;

        return view('tasks.index', compact('tasks', 'categories', 'category'));
    }
}
