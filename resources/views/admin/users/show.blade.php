@extends('layouts.app')

@section('title', 'Détails Utilisateur - ' . $user->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-900">
            ← Retour à la liste
        </a>
        
        <!-- Navigation Buttons -->
        <div class="flex gap-2">
            @if($previousUser)
                <a href="{{ route('admin.users.show', $previousUser) }}" class="px-4 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition font-medium">
                    ← Précédent
                </a>
            @else
                <button disabled class="px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed font-medium">
                    ← Précédent
                </button>
            @endif
            
            @if($nextUser)
                <a href="{{ route('admin.users.show', $nextUser) }}" class="px-4 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition font-medium">
                    Suivant →
                </a>
            @else
                <button disabled class="px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed font-medium">
                    Suivant →
                </button>
            @endif
        </div>
    </div>

    <!-- User Info Card -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <div class="h-20 w-20 rounded-full bg-blue-600 flex items-center justify-center text-white text-2xl font-semibold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="ml-6">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="text-gray-600">{{ $user->email }}</p>
                    <div class="mt-2">
                        @if($user->isAdmin())
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                Admin
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Utilisateur
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.users.edit-profile', $user) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Éditer Profil
                </a>
                <a href="{{ route('admin.users.edit-password', $user) }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                    Changer Mot de Passe
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-sm text-gray-600">Inscrit le</div>
                <div class="text-lg font-semibold">{{ $user->created_at->format('d/m/Y') }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-sm text-gray-600">Total Tâches</div>
                <div class="text-lg font-semibold text-blue-600">{{ $user->tasks->count() }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-sm text-gray-600">Total Catégories</div>
                <div class="text-lg font-semibold text-green-600">{{ $user->categories->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Categories -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Catégories ({{ $user->categories->count() }})</h2>
        
        @if($user->categories->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($user->categories as $category)
                    <a href="{{ route('admin.categories.show', $category) }}" class="border rounded-lg p-4 hover:shadow-lg hover:bg-blue-50 transition cursor-pointer">
                        <div class="flex items-center mb-2">
                            <div class="w-4 h-4 rounded-full mr-2" style="background-color: {{ $category->color }}"></div>
                            <h3 class="font-semibold text-blue-600 hover:text-blue-900">{{ $category->name }}</h3>
                        </div>
                        @if($category->description)
                            <p class="text-sm text-gray-600">{{ Str::limit($category->description, 50) }}</p>
                        @endif
                        <div class="text-sm text-gray-500 mt-2">
                            {{ $category->tasks->count() }} tâche(s)
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">Aucune catégorie créée</p>
        @endif
    </div>

    <!-- Tasks -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Tâches ({{ $user->tasks()->count() }})</h2>
        
        @if($tasks->total() > 0)
            <div class="space-y-3">
                @foreach($tasks as $task)
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
                                    @if($task->category)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-xs">
                                            <div class="w-2 h-2 rounded-full" style="background-color: {{ $task->category->color }}"></div>
                                            {{ $task->category->name }}
                                        </span>
                                    @endif
                                    @if($task->due_date)
                                        <span class="text-xs text-gray-500">
                                            📅 {{ $task->due_date->format('d/m/Y') }}
                                        </span>
                                    @endif
                                    @if($task->completed_at)
                                        <span class="text-xs text-green-600">✓ Complétée</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Load More Button -->
            @if($tasks->hasMorePages())
                <div class="flex justify-center mt-6 pt-6 border-t">
                    <a href="{{ $tasks->nextPageUrl() }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        Afficher plus de tâches ↓
                    </a>
                </div>
            @endif
        @else
            <p class="text-gray-500">Aucune tâche créée</p>
        @endif
    </div>
</div>
@endsection
