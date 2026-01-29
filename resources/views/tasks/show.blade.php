@extends('layouts.app')

@section('title', $task->title)

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('tasks.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">← Retour</a>
    
    <div class="bg-white rounded-lg shadow p-8">
        <div class="flex items-start justify-between mb-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold text-gray-900 {{ $task->isCompleted() ? 'line-through text-gray-500' : '' }}">
                        {{ $task->title }}
                    </h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        @switch($task->status)
                            @case('pending')
                                En attente
                                @break
                            @case('in_progress')
                                En cours
                                @break
                            @case('completed')
                                Complétée
                                @break
                        @endswitch
                    </span>
                </div>
                <p class="text-gray-600">Créée le {{ $task->created_at->format('d/m/Y à H:i') }}</p>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('tasks.edit', $task) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    ✎ Éditer
                </a>
                <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                        🗑 Supprimer
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            @if ($task->category)
                <div>
                    <h3 class="text-sm font-medium text-gray-600">Catégorie</h3>
                    <p class="mt-1 flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $task->category->color }}"></div>
                        <a href="{{ route('tasks.filterByCategory', $task->category) }}" class="text-blue-600 hover:underline">
                            {{ $task->category->name }}
                        </a>
                    </p>
                </div>
            @endif

            @if ($task->due_date)
                <div>
                    <h3 class="text-sm font-medium text-gray-600">Date d'échéance</h3>
                    <p class="mt-1 {{ $task->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                        {{ $task->due_date->format('d/m/Y à H:i') }}
                        @if ($task->isOverdue())
                            <span class="text-xs">⚠️ En retard!</span>
                        @endif
                    </p>
                </div>
            @endif

            @if ($task->completed_at)
                <div>
                    <h3 class="text-sm font-medium text-gray-600">Complétée le</h3>
                    <p class="mt-1 text-green-600">{{ $task->completed_at->format('d/m/Y à H:i') }}</p>
                </div>
            @endif
        </div>

        @if ($task->description)
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-600 mb-2">Description</h3>
                <div class="prose prose-sm max-w-none text-gray-700 bg-gray-50 p-4 rounded-lg whitespace-pre-wrap">
                    {{ $task->description }}
                </div>
            </div>
        @endif

        <div class="flex gap-3 pt-4 border-t">
            <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-lg transition
                    {{ $task->isCompleted() ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                    {{ $task->isCompleted() ? '↩️ Marquer comme incomplet' : '✓ Marquer comme complétée' }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
