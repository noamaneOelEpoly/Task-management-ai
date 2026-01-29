<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    // List all categories with sorting and filtering
    public function index(Request $request)
    {
        $query = Category::with(['user', 'tasks']);
        
        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['name', 'created_at', 'color'])) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $categories = $query->withCount('tasks')->paginate(20)->appends($request->query());
        $users = User::where('role', 'user')->get();
        
        return view('admin.categories.index', compact('categories', 'users'));
    }

    // Create category form
    public function create(Request $request)
    {
        $users = User::where('role', 'user')->get();
        $selectedUserId = $request->get('user_id');
        
        return view('admin.categories.create', compact('users', 'selectedUserId'));
    }

    // Show category details
    public function show(Category $category)
    {
        $category->load(['user', 'tasks']);
        return view('admin.categories.show', compact('category'));
    }

    // Store new category
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Catégorie créée avec succès');
    }

    // Edit category form
    public function edit(Category $category)
    {
        $users = User::where('role', 'user')->get();
        
        return view('admin.categories.edit', compact('category', 'users'));
    }

    // Update category
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Catégorie mise à jour avec succès');
    }

    // Delete category
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Catégorie supprimée avec succès');
    }
}
