@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="max-w-4xl">
    <a href="{{ route('categories.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">← Retour</a>
    
    <div class="bg-white rounded-lg shadow p-8 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-4 h-4 rounded" style="background-color: {{ $category->color }}"></div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
                </div>
                @if ($category->description)
                    <p class="text-gray-600">{{ $category->description }}</p>
                @endif
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('categories.edit', $category) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    ✎ Éditer
                </a>
                <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr? Toutes les tâches seront supprimées.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                        🗑 Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Tâches ({{ $tasks->total() }})</h2>
        </div>

        @forelse ($tasks as $task)
            <div class="border-b last:border-b-0 p-6 hover:bg-gray-50 transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="inline">
                                @csrf
                                <button type="submit" class="flex-shrink-0 w-6 h-6 rounded border-2 flex items-center justify-center transition
                                    {{ $task->isCompleted() ? 'bg-green-600 border-green-600' : 'border-gray-300 hover:border-green-600' }}">
                                    @if ($task->isCompleted())
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @endif
                                </button>
                            </form>
                            
                            <div>
                                <a href="{{ route('tasks.show', $task) }}" class="font-semibold text-gray-900 hover:text-blue-600 {{ $task->isCompleted() ? 'line-through text-gray-500' : '' }}">
                                    {{ $task->title }}
                                </a>
                                @if ($task->description)
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($task->description, 100) }}</p>
                                @endif
                                
                                @if ($task->due_date)
                                    <p class="text-xs font-medium mt-2 {{ $task->isOverdue() ? 'text-red-600' : 'text-gray-600' }}">
                                        @if ($task->isOverdue())
                                            ⚠️ En retard: 
                                        @endif
                                        {{ $task->due_date->format('d/m/Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 ml-4">
                        <a href="{{ route('tasks.edit', $task) }}" class="text-blue-600 hover:text-blue-900 p-2">
                            ✎
                        </a>
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 p-2">
                                🗑
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center">
                <p class="text-gray-500">Aucune tâche dans cette catégorie</p>
            </div>
        @endforelse

        @if ($tasks->hasPages())
            <div class="px-6 py-4">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
