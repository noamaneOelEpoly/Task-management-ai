@extends('layouts.app')

@section('title', 'Éditer - ' . $category->name)

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('categories.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">← Retour</a>
    
    <div class="bg-white rounded-lg shadow p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Éditer: {{ $category->name }}</h1>

        <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nom *
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description', $category->description) }}</textarea>
            </div>

            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 mb-1">
                    Couleur
                </label>
                <div class="flex gap-2 items-center">
                    <input type="color" id="color" name="color" value="{{ old('color', $category->color) }}"
                           class="w-16 h-10 border border-gray-300 rounded-lg cursor-pointer">
                    <span id="colorValue" class="text-sm text-gray-600">{{ old('color', $category->color) }}</span>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                    Mettre à jour
                </button>
                <a href="{{ route('categories.index') }}" class="flex-1 bg-gray-200 text-gray-900 py-2 rounded-lg hover:bg-gray-300 transition font-medium text-center">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const colorInput = document.getElementById('color');
    const colorValue = document.getElementById('colorValue');
    
    colorInput.addEventListener('input', (e) => {
        colorValue.textContent = e.target.value;
    });
</script>
@endsection
