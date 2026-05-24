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

        // Intentar autenticar
        if (Auth::attempt(['email_institucional' => $credentials['email'], 'password' => $credentials['password']], $request->filled('remember'))) {
            
            $request->session()->regenerate();

            // ✅ DETECTAR SI TIENE DOBLE ROL (Tutor <-> Tribunal)
            if ($this->tieneDobleRol($user)) {
                return redirect()->route('auth.role.select');
            }

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
                return redirect()->route('home');
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        // Invalidar la sesión
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redirigir a la Landing Page
        return redirect()->route('home');
    }

    /**
     * Verificar si el usuario tiene asignaciones cruzadas (Doble Rol)
     */
    private function tieneDobleRol(User $user)
    {
        $rol = $user->rol?->nombre;

        // Si es Tutor, verificar si también es Tribunal
        if ($rol === 'tutor') {
            return \App\Models\AsignacionTribunal::where('tribunal_id', $user->id)->where('activo', true)->exists();
        }

        // Si es Tribunal, verificar si también es Tutor
        if ($rol === 'tribunal') {
            return \App\Models\AsignacionTutor::where('tutor_id', $user->id)->where('activo', true)->exists();
        }

        return false;
    }

    /**
     * Mostrar vista de selección de rol
     */
    public function showRoleSelect()
    {
        $user = Auth::user();
        $rol = $user->rol?->nombre;
        
        // Determinar qué opciones mostrar
        $opciones = [];
        
        // Siempre mostrar su rol principal
        $opciones[] = [
            'key' => $rol, 
            'label' => ucfirst($rol), 
            'route' => "{$rol}.dashboard", 
            'icon' => 'fa-check-circle',
            'desc' => "Acceder como {$rol}"
        ];

        // Si es Tutor, mostrar opción Tribunal
        if ($rol === 'tutor') {
            $opciones[] = [
                'key' => 'tribunal', 
                'label' => 'Tribunal', 
                'route' => 'tribunal.dashboard', 
                'icon' => 'fa-gavel',
                'desc' => 'Revisar documentos como jurado'
            ];
        }
        
        // Si es Tribunal, mostrar opción Tutor
        if ($rol === 'tribunal') {
            $opciones[] = [
                'key' => 'tutor', 
                'label' => 'Tutor', 
                'route' => 'tutor.dashboard', 
                'icon' => 'fa-chalkboard-teacher',
                'desc' => 'Gestionar mis tutorados'
            ];
        }

        return view('auth.select-role', compact('user', 'opciones'));
    }

    /**
     * Procesar selección de rol
     */
    public function selectRole(Request $request)
    {
        $request->validate([
            'selected_role' => 'required|string|in:tutor,tribunal'
        ]);

        $user = Auth::user();
        $rolPrincipal = $user->rol?->nombre;
        $seleccion = $request->input('selected_role');

        // Validar que la selección sea válida para este usuario
        $esValido = false;
        
        if ($rolPrincipal === 'tutor' && $seleccion === 'tribunal') {
            $esValido = \App\Models\AsignacionTribunal::where('tribunal_id', $user->id)->where('activo', true)->exists();
        } elseif ($rolPrincipal === 'tribunal' && $seleccion === 'tutor') {
            $esValido = \App\Models\AsignacionTutor::where('tutor_id', $user->id)->where('activo', true)->exists();
        } elseif ($rolPrincipal === $seleccion) {
            $esValido = true;
        }

        if (!$esValido) {
            return back()->with('error', 'No tienes acceso a ese rol.');
        }

        return redirect()->route("{$seleccion}.dashboard");
    }
}