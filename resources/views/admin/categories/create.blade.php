@extends('layouts.app')

@section('title', 'Créer une Catégorie')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition font-medium mb-4">
        ← Retour
    </a>
    
    <div class="bg-white rounded-lg shadow p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Créer une nouvelle catégorie</h1>

        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Utilisateur *
                </label>
                <select id="user_id" name="user_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('user_id') border-red-500 @enderror">
                    <option value="">-- Sélectionner un utilisateur --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $selectedUserId) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nom *
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                       placeholder="Ex: Travail, Personnel, Shopping...">
                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Description optionnelle...">{{ old('description') }}</textarea>
                @error('description') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 mb-1">
                    Couleur *
                </label>
                <div class="flex items-center gap-3">
                    <input type="color" id="color" name="color" value="{{ old('color', '#3B82F6') }}" required
                           class="w-16 h-10 rounded border border-gray-300 cursor-pointer">
                    <span id="colorPreview" class="px-4 py-2 rounded text-white" style="background-color: {{ old('color', '#3B82F6') }};">
                        Aperçu
                    </span>
                </div>
                @error('color') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                    Créer la catégorie
                </button>
                <a href="{{ route('admin.categories.index') }}" class="flex-1 bg-gray-200 text-gray-900 py-2 rounded-lg hover:bg-gray-300 transition font-medium text-center">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const colorInput = document.getElementById('color');
    const colorPreview = document.getElementById('colorPreview');
    
    colorInput.addEventListener('change', function() {
        colorPreview.style.backgroundColor = this.value;
    });
</script>
@endsection
