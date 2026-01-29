@extends('layouts.app')

@section('title', 'Catégorie - ' . $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('admin.categories.index') }}" class="text-blue-600 hover:text-blue-900">
            ← Retour à la gestion
        </a>
        
        <div class="flex gap-2">
            <a href="{{ route('admin.categories.edit', $category) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Éditer
            </a>
            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Supprimer
                </button>
            </form>
        </div>
    </div>

    <!-- Category Info Card -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center mb-4">
            <div class="w-16 h-16 rounded-lg mr-6" style="background-color: {{ $category->color }};"></div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
                <p class="text-gray-600">Propriétaire: <strong>{{ $category->user->name }}</strong></p>
            </div>
        </div>

        @if($category->description)
            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                <p class="text-gray-700">{{ $category->description }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-sm text-gray-600">Couleur</div>
                <div class="text-lg font-semibold mt-2" style="color: {{ $category->color }};">
                    {{ $category->color }}
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-sm text-gray-600">Créée le</div>
                <div class="text-lg font-semibold">{{ $category->created_at->format('d/m/Y') }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-sm text-gray-600">Total Tâches</div>
                <div class="text-lg font-semibold text-blue-600">{{ $category->tasks->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Tasks in this category -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Tâches ({{ $category->tasks->count() }})</h2>

        @if($category->tasks->count() > 0)
            <div class="space-y-3">
                @foreach($category->tasks->sortByDesc('created_at') as $task)
                    <div class="border rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-semibold {{ $task->completed_at ? 'line-through text-gray-500' : '' }}">
                                    {{ $task->title }}
                                </h3>
                                @if($task->description)
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($task->description, 100) }}</p>
                                @endif
                                <div class="flex items-center gap-3 mt-2">
                                    <span class="text-sm text-gray-600">
                                        Par: <strong>{{ $task->user->name }}</strong>
                                    </span>
                                    @if($task->due_date)
                                        <span class="text-xs text-gray-500">
                                            📅 {{ $task->due_date->format('d/m/Y H:i') }}
                                        </span>
                                    @endif
                                    @if($task->completed_at)
                                        <span class="text-xs text-green-600">✓ Complétée</span>
                                    @else
                                        <span class="text-xs text-yellow-600">⏳ En cours</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">Aucune tâche dans cette catégorie</p>
        @endif
    </div>
</div>
@endsection
