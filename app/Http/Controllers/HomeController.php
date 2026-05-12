<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $data = [
            'appName' => 'DocGest',
            'tagline' => 'Gestión documental para proyectos de grado',
            'description' => 'Sistema de revisión iterativa con flujo Tutor → Tribunales → Aprobación.'
        ];
        
        return view('home', $data);
    }
}