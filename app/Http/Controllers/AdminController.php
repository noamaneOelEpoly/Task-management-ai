<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Admin dashboard
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalTasks = Task::count();
        $totalCategories = Category::count();
        
        return view('admin.dashboard', compact('totalUsers', 'totalTasks', 'totalCategories'));
    }

    // List all users
    public function index()
    {
        $users = User::with(['tasks', 'categories'])->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    // Show specific user
    public function show(User $user)
    {
        $user->load(['categories']);
        
        // Paginate tasks (10 per page)
        $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);
        
        // Get all users ordered by ID
        $allUsers = User::orderBy('id')->pluck('id')->toArray();
        $currentIndex = array_search($user->id, $allUsers);
        
        // Get previous and next users
        $previousUser = null;
        $nextUser = null;
        
        if ($currentIndex > 0) {
            $previousUser = User::find($allUsers[$currentIndex - 1]);
        }
        
        if ($currentIndex < count($allUsers) - 1) {
            $nextUser = User::find($allUsers[$currentIndex + 1]);
        }
        
        return view('admin.users.show', compact('user', 'previousUser', 'nextUser', 'tasks'));
    }

    // Show edit form for user
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // Update user
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:user,admin'
        ]);

        $user->update($validated);
        
        return redirect()->route('admin.users.show', $user)->with('success', 'User updated successfully');
    }

    // Delete user
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }

    // View all tasks from all users
    public function allTasks()
    {
        $tasks = Task::with(['user', 'category'])->latest()->paginate(20);
        return view('admin.tasks.index', compact('tasks'));
    }

    // View all categories from all users
    public function allCategories()
    {
        $categories = Category::with('user')->latest()->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }
}
