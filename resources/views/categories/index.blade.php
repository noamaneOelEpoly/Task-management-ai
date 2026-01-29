@extends('layouts.app')

@section('title', 'Catégories')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Catégories</h1>
        <a href="{{ route('categories.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
            + Nouvelle Catégorie
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($categories as $category)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
            <div class="h-2" style="background-color: {{ $category->color }}"></div>
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $category->name }}</h2>
                
                @if ($category->description)
                    <p class="text-gray-600 text-sm mb-4">{{ Str::limit($category->description, 100) }}</p>
                @endif
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">
                        {{ $category->tasks_count }} tâche{{ $category->tasks_count !== 1 ? 's' : '' }}
                    </span>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('categories.edit', $category) }}" class="text-blue-600 hover:text-blue-900 p-2">
                            ✎
                        </a>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 p-2">
                                🗑
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('categories.show', $category) }}" class="block px-6 py-3 bg-gray-50 hover:bg-gray-100 text-blue-600 font-medium text-center transition">
                Voir les tâches →
            </a>
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
            <p class="text-gray-500 mb-4">Aucune catégorie créée</p>
            <a href="{{ route('categories.create') }}" class="text-blue-600 hover:underline">
                Créer votre première catégorie
            </a>
        </div>
    @endforelse
    </div>

    @if(isset($categories) && $categories->hasPages())
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
