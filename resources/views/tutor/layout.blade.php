<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DocGest - Tutor')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#6D2121',
                        secondary: '#511818',
                        accent: '#7A231E',
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Gradiente personalizado del sidebar */
        .sidebar-gradient {
            background: linear-gradient(180deg, 
                #000000 0%, 
                #6D2121 38%, 
                #511818 100%);
        }
        
        /* Gradiente personalizado del fondo principal */
        .main-gradient {
            background: linear-gradient(180deg, 
                #290C0A 0%, 
                #611C18 24%, 
                #7A231E 100%);
        }
        
        /* Cards con fondo negro y poca opacidad */
        .card-dark {
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
        }
        
        /* Estilos del navbar-brand del sidebar */
        .navbar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .brand-logos {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .brand-logos img:first-child {
            height: 50px;
            width: auto;
            object-fit: contain;
        }
        
        .brand-text h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #FFFFFF;
            margin: 0;
            line-height: 1.2;
        }
        
        .brand-text p {
            font-size: 0.75rem;
            color: #9CA3AF;
            margin: 0.25rem 0 0 0;
            font-weight: 400;
        }
        
        .brand-separator {
            display: block;
            height: 1px;
            background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
            margin: 1rem 0;
        }
        
        .brand-univalle img {
            height: 40px;
            width: auto;
            object-fit: contain;
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }
        
        .sidebar-link:hover {
            background: linear-gradient(90deg, rgba(109, 33, 33, 0.4) 0%, transparent 100%);
            border-right: 3px solid #6D2121;
        }
        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(109, 33, 33, 0.6) 0%, transparent 100%);
            border-right: 3px solid #8B2D2D;
            font-weight: 600;
        }
    </style>
    
    @stack('styles')
</head>
<body class="main-gradient min-h-screen">
    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR -->
        <aside class="w-64 sidebar-gradient shadow-lg flex-shrink-0 hidden md:block">
            <div class="navbar-brand">
                <div class="brand-logos">
                    <img src="{{ asset('img/logo_docgest.png') }}" alt="DocGest Logo">
                    <div class="brand-text">
                        <h1>DocGest · Univalle</h1>
                        <p>Sistema de Gestión Académica</p>
                    </div>
                </div>
                <span class="brand-separator"></span>
                <div class="brand-univalle">
                    <img src="{{ asset('img/logo_univalle.png') }}" alt="Univalle">
                </div>
            </div>
            
            <nav class="mt-6">
                <a href="{{ route('tutor.dashboard') }}" 
                   class="sidebar-link flex items-center px-6 py-3 text-white {{ request()->routeIs('tutor.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home w-6"></i>
                    <span>Inicio</span>
                </a>
                
                <a href="{{ route('tutor.tutorados') }}" 
                   class="sidebar-link flex items-center px-6 py-3 text-white {{ request()->routeIs('tutor.tutorados') ? 'active' : '' }}">
                    <i class="fas fa-users w-6"></i>
                    <span>Tutorados</span>
                </a>
                
                <a href="{{ route('tutor.documentos') }}" 
                   class="sidebar-link flex items-center px-6 py-3 text-white {{ request()->routeIs('tutor.documentos') ? 'active' : '' }}">
                    <i class="fas fa-file-alt w-6"></i>
                    <span>Lista Documentos</span>
                </a>
                
               
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- HEADER / NAVBAR -->
            <header class="bg-black shadow-sm border-b border-gray-800">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button class="md:hidden text-white focus:outline-none mr-4">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>
                        <h2 class="text-xl font-semibold text-white">@yield('page-title', 'Dashboard')</h2>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <button class="text-white hover:text-red-500 focus:outline-none">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                            </button>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-medium text-white">{{ auth()->user()->name ?? 'Tutor' }}</p>
                                <p class="text-xs text-gray-400">Docente Tutor</p>
                            </div>
                            <div class="h-10 w-10 rounded-full bg-red-700 flex items-center justify-center text-white font-semibold">
                                {{ substr(auth()->user()->name ?? 'T', 0, 1) }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
                @if(session('success'))
                    <div class="bg-green-900 bg-opacity-70 border border-green-600 text-green-100 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-900 bg-opacity-70 border border-red-600 text-red-100 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>