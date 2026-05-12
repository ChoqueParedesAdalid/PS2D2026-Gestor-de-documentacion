<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar login con validación de correos institucionales
     */
    public function login(Request $request)
    {
        // Validar campos
        $credentials = $request->validate([
            'email' => ['required', 'email', 'ends_with:@univalle.edu,@est.univalle.edu'],
            'password' => ['required'],
        ], [
            'email.ends_with' => 'Debes ingresar un correo institucional válido de la Universidad del Valle.',
        ]);

        // Verificar que el usuario exista y esté activo
        $user = User::where('email_institucional', $credentials['email'])
                    ->where('activo', true)
                    ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'No existe una cuenta asociada a este correo institucional.',
            ]);
        }

        // Intentar autenticar (usando password_hash como campo de contraseña)
        if (Auth::attempt(['email_institucional' => $credentials['email'], 'password' => $credentials['password']], $request->filled('remember'))) {
            
            $request->session()->regenerate();

            // Redirección según rol del usuario
            return $this->redireccionarPorRol($user);
        }

        // Si falla la contraseña
        return back()->withErrors([
            'password' => 'La contraseña ingresada es incorrecta.',
        ])->onlyInput('email');
    }

    /**
     * Redirección según el rol del usuario
     */
    protected function redireccionarPorRol(User $user)
    {
        switch ($user->rol?->nombre) {
            case 'tutor':
                return redirect()->route('tutor.dashboard');
            
            case 'estudiante':
                return redirect()->route('estudiante.dashboard');
            
            case 'docente_cargo':
                return redirect()->route('docente.dashboard');
            
            case 'tribunal':
                return redirect()->route('tribunal.dashboard');
            
            case 'director':
                return redirect()->route('director.dashboard');
            
            default:
                // Redirección por defecto si el rol no está definido
                return redirect()->route('home');
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}