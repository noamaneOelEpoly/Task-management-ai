@extends('layouts.app')

@section('title', 'Gestion des Catégories')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Gestion des Catégories</h1>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            + Nouvelle Catégorie
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters and Sorting -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Utilisateur</label>
                <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">-- Tous --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trier par</label>
                <select name="sort_by" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date de création</option>
                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nom</option>
                    <option value="color" {{ request('sort_by') == 'color' ? 'selected' : '' }}>Couleur</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ordre</label>
                <select name="sort_order" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Décroissant</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Croissant</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Couleur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Utilisateur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tâches</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="w-8 h-8 rounded-full border-2 border-gray-300" style="background-color: {{ $category->color }};"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <span class="font-medium text-gray-900">{{ $category->name }}</span>
                                @if($category->description)
                                    <p class="text-sm text-gray-500">{{ Str::limit($category->description, 50) }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-700">{{ $category->user->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $category->tasks_count ?? $category->tasks->count() }} tâche(s)
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    Éditer
                                </a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" 
                                      onsubmit="return confirm('Êtes-vous sûr ? Les tâches associées seront conservées.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Aucune catégorie trouvée.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
        <div class="flex justify-between items-center mt-6">
            @if($categories->onFirstPage())
                <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded cursor-not-allowed">
                    ← Précédent
                </span>
            @else
                <a href="{{ $categories->previousPageUrl() }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    ← Précédent
                </a>
            @endif

            <span class="text-gray-600">
                Page {{ $categories->currentPage() }} sur {{ $categories->lastPage() }}
            </span>

            @if($categories->hasMorePages())
                <a href="{{ $categories->nextPageUrl() }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Suivant →
                </a>
            @else
                <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded cursor-not-allowed">
                    Suivant →
                </span>
            @endif
        </div>
    @endif
</div>
@endsection
