<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminTaskController extends Controller
{
    // List all tasks with sorting and filtering
    public function index(Request $request)
    {
        $query = Task::with(['user', 'category']);
        
        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['title', 'created_at', 'due_date', 'completed'])) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $tasks = $query->paginate(20)->appends($request->query());
        $users = User::where('role', 'user')->get();
        $categories = Category::get();
        
        return view('admin.tasks.index', compact('tasks', 'users', 'categories'));
    }

    // Create task form
    public function create(Request $request)
    {
        $users = User::where('role', 'user')->get();
        $categories = Category::get();
        $selectedUserId = $request->get('user_id');
        
        return view('admin.tasks.create', compact('users', 'categories', 'selectedUserId'));
    }

    // Store new task
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'due_date' => 'nullable|date',
            'completed' => 'boolean',
        ]);

        Task::create($validated);

        return redirect()->route('admin.tasks.index')
                        ->with('success', 'Tâche créée avec succès');
    }

    // Edit task form
    public function edit(Task $task)
    {
        $users = User::where('role', 'user')->get();
        $categories = Category::get();
        
        return view('admin.tasks.edit', compact('task', 'users', 'categories'));
    }

    // Update task
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'due_date' => 'nullable|date',
            'completed' => 'boolean',
        ]);

        $task->update($validated);

        return redirect()->route('admin.tasks.index')
                        ->with('success', 'Tâche mise à jour avec succès');
    }

    // Delete task
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('admin.tasks.index')
                        ->with('success', 'Tâche supprimée avec succès');
    }
}
