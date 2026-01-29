<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserProfileController extends Controller
{
    // Edit user profile form
    public function editProfile(User $user)
    {
        return view('admin.users.edit-profile', compact('user'));
    }

    // Update user profile
    public function updateProfile(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:user,admin',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)
                        ->with('success', 'Profil utilisateur mis à jour avec succès');
    }

    // Edit password form
    public function editPassword(User $user)
    {
        return view('admin.users.edit-password', compact('user'));
    }

    // Update password
    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.show', $user)
                        ->with('success', 'Mot de passe mis à jour avec succès');
    }
}
