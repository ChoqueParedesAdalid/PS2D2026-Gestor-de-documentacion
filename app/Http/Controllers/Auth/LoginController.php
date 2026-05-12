<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    // Usuarios simulados por rol
    private $usuarios = [
        ['email' => 'docente@univalle.edu',   'password' => '123456', 'rol' => 'docente'],
        ['email' => 'director@univalle.edu',  'password' => '123456', 'rol' => 'director'],
        ['email' => 'tutor@univalle.edu',     'password' => '123456', 'rol' => 'tutor'],
        ['email' => 'jurado@univalle.edu',    'password' => '123456', 'rol' => 'jurado'],
        ['email' => 'estudiante@univalle.edu','password' => '123456', 'rol' => 'estudiante'],
    ];

    public function showLoginForm()
    {
        if (Session::has('usuario')) {
            return $this->redirigirPorRol(Session::get('usuario')['rol']);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        foreach ($this->usuarios as $usuario) {
            if ($usuario['email'] === $request->email && 
                $usuario['password'] === $request->password) {
                
                Session::put('usuario', $usuario);
                return $this->redirigirPorRol($usuario['rol']);
            }
        }

        return back()->withErrors([
            'email' => 'Credenciales incorrectas.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Session::forget('usuario');
        return redirect()->route('login');
    }

    private function redirigirPorRol($rol)
    {
        return match($rol) {
            'docente'     => redirect()->route('docente.dashboard'),
            'director'    => redirect()->route('director.dashboard'),
            'tutor'       => redirect()->route('tutor.dashboard'),
            'jurado'      => redirect()->route('jurado.dashboard'),
            'estudiante'  => redirect()->route('estudiante.dashboard'),
            default       => redirect()->route('login'),
        };
    }
}