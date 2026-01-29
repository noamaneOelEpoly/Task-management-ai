<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Back Button -->
                    <div class="mb-6">
                        <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to User Details
                        </a>
                    </div>

                    <!-- Edit Form -->
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PATCH')

                        <div class="space-y-6">
                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                                    :value="old('name', $user->name)" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" 
                                    :value="old('email', $user->email)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <!-- Role -->
                            <div>
                                <x-input-label for="role" :value="__('Role')" />
                                <select id="role" name="role" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('role')" />
                            </div>

                            <!-- Created At (Read-only) -->
                            <div>
                                <x-input-label for="created_at" :value="__('Member Since')" />
                                <x-text-input id="created_at" type="text" class="mt-1 block w-full bg-gray-100" 
                                    :value="$user->created_at->format('F d, Y')" readonly />
                            </div>

                            <!-- User Statistics (Read-only) -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Total Tasks</label>
                                    <div class="mt-1 text-2xl font-semibold text-gray-900">
                                        {{ $user->tasks()->count() }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Total Categories</label>
                                    <div class="mt-1 text-2xl font-semibold text-gray-900">
                                        {{ $user->categories()->count() }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Completed Tasks</label>
                                    <div class="mt-1 text-2xl font-semibold text-gray-900">
                                        {{ $user->tasks()->whereNotNull('completed_at')->count() }}
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between pt-4">
                                <div class="flex gap-4">
                                    <x-primary-button>
                                        {{ __('Save Changes') }}
                                    </x-primary-button>

                                    <a href="{{ route('admin.users.show', $user) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Cancel') }}
                                    </a>
                                </div>

                                <!-- Additional Actions -->
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.users.edit-profile', $user) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                        Edit Profile Details
                                    </a>
                                    
                                    <a href="{{ route('admin.users.edit-password', $user) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                                        Change Password
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
