@extends('layouts.app')

@section('title', 'Mes Tâches')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-4 gap-5 mx-6 my-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-medium">Total</h3>
        <p class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-medium">Complétées</h3>
        <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['completed'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-medium">En retard</h3>
        <p class="text-3xl font-bold text-red-600 mt-2">{{ $stats['overdue'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-medium">Progression</h3>
        @php $percentage = $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0; @endphp
        <div class="mt-2">
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
            </div>
            <p class="text-sm text-gray-600 mt-1">{{ $percentage }}%</p>
        </div>
    </div>
</div>

<div class="flex justify-between items-center my-6 mx-6">
    <h1 class="text-3xl font-bold text-gray-900">Mes Tâches</h1>
    <a href="{{ route('tasks.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
        + Nouvelle Tâche
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mx-6 my-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Catégories</h3>
            <div class="space-y-2">
                @forelse ($categories as $cat)
                    <a href="{{ route('tasks.filterByCategory', $cat) }}" 
                       class="flex items-center gap-2 p-2 rounded hover:bg-gray-100 transition">
                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $cat->color }}"></div>
                        <span class="text-sm text-gray-700">{{ $cat->name }}</span>
                    </a>
                @empty
                    <p class="text-sm text-gray-500">Aucune catégorie</p>
                @endforelse
                <a href="{{ route('categories.create') }}" class="text-blue-600 text-sm hover:underline mt-4 block">
                    + Nouvelle catégorie
                </a>
            </div>
        </div>
    </div>

    <div class="lg:col-span-3">
        <div class="bg-white rounded-lg shadow overflow-hidden">
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
                                    <h3 class="font-semibold text-gray-900 {{ $task->isCompleted() ? 'line-through text-gray-500' : '' }}">
                                        {{ $task->title }}
                                    </h3>
                                    @if ($task->description)
                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($task->description, 100) }}</p>
                                    @endif
                                    
                                    <div class="flex items-center gap-4 mt-2">
                                        @if ($task->category)
                                            <span class="inline-flex items-center gap-2 px-2 py-1 bg-gray-100 rounded text-xs">
                                                <div class="w-2 h-2 rounded-full" style="background-color: {{ $task->category->color }}"></div>
                                                {{ $task->category->name }}
                                            </span>
                                        @endif
                                        
                                        @if ($task->due_date)
                                            <span class="text-xs font-medium {{ $task->isOverdue() ? 'text-red-600' : 'text-gray-600' }}">
                                                @if ($task->isOverdue())
                                                    ⚠️ En retard: 
                                                @endif
                                                {{ $task->due_date->format('d/m/Y H:i') }}
                                            </span>
                                        @endif
                                    </div>
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
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-gray-500">Aucune tâche trouvée. Créez votre première tâche!</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            @if($tasks->hasPages())
                <div class="flex justify-between items-center">
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
    </div>
</div>
@endsection
