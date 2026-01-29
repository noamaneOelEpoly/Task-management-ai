@extends('layouts.app')

@section('title', 'Gestion des Tâches')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Gestion des Tâches</h1>
        <a href="{{ route('admin.tasks.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            + Nouvelle Tâche
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters and Sorting -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.tasks.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                    <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>Titre</option>
                    <option value="due_date" {{ request('sort_by') == 'due_date' ? 'selected' : '' }}>Date d'échéance</option>
                    <option value="completed" {{ request('sort_by') == 'completed' ? 'selected' : '' }}>Statut</option>
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

    <!-- Tasks Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Titre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Utilisateur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Catégorie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Échéance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($tasks as $task)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900">{{ $task->title }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-700">{{ $task->user->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($task->category)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                      style="background-color: {{ $task->category->color }}30; color: {{ $task->category->color }};">
                                    {{ $task->category->name }}
                                </span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($task->completed)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    ✓ Complétée
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    En cours
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            @if($task->due_date)
                                {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y H:i') }}
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.tasks.edit', $task) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    Éditer
                                </a>
                                <form method="POST" action="{{ route('admin.tasks.destroy', $task) }}" class="inline" 
                                      onsubmit="return confirm('Êtes-vous sûr ?');">
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
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Aucune tâche trouvée.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($tasks->hasPages())
        <div class="flex justify-between items-center mt-6">
            @if($tasks->onFirstPage())
                <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded cursor-not-allowed">
                    ← Précédent
                </span>
            @else
                <a href="{{ $tasks->previousPageUrl() }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    ← Précédent
                </a>
            @endif

            <span class="text-gray-600">
                Page {{ $tasks->currentPage() }} sur {{ $tasks->lastPage() }}
            </span>

            @if($tasks->hasMorePages())
                <a href="{{ $tasks->nextPageUrl() }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
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
