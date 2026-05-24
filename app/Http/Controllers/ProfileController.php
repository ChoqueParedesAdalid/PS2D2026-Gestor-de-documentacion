<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Mostrar vista de perfil
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Actualizar información personal
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email_institucional' => 'required|email|unique:usuarios,email_institucional,' . $user->id,
        ]);
        
        $user->update($validated);
        
        return back()->with('success', 'Información actualizada correctamente.');
    }

    /**
     * Actualizar contraseña
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_actual' => ['required', 'current_password'],
            'password_nuevo' => ['required', 'confirmed', Password::defaults()],
        ], [
            'password_actual.current_password' => 'La contraseña actual no es correcta.',
            'password_nuevo.confirmed' => 'Las contraseñas nuevas no coinciden.',
        ]);
        
        $user = Auth::user();
        $user->password_hash = Hash::make($request->password_nuevo);
        $user->save();
        
        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}