<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DocGest - Estudiante')</title>
    
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
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { primary: '#6D2121', secondary: '#511818', accent: '#7A231E' }
                }
            }
        }
    </script>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body, html { font-family: 'Inter', sans-serif; overflow-x: hidden; height: 100%; width: 100%; }
        [x-cloak] { display: none !important; }
        .sidebar-gradient { background: linear-gradient(180deg, #000000 0%, #6D2121 38%, #511818 100%); }
        .main-gradient { background: linear-gradient(180deg, #290C0A 0%, #611C18 24%, #7A231E 100%); min-height: 100vh; }
        .card-dark { background-color: rgba(0, 0, 0, 0.7); backdrop-filter: blur(10px); }
        .navbar-brand { padding: 1.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .brand-logos { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; }
        .brand-logos img:first-child { height: 50px; width: auto; object-fit: contain; }
        .brand-text h1 { font-size: 1.25rem; font-weight: 700; color: #FFFFFF; margin: 0; line-height: 1.2; }
        .brand-text p { font-size: 0.75rem; color: #9CA3AF; margin: 0.25rem 0 0 0; font-weight: 400; }
        .brand-separator { display: block; height: 1px; background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%); margin: 1rem 0; }
        .brand-univalle img { height: 40px; width: auto; object-fit: contain; filter: brightness(0) invert(1); opacity: 0.9; }
        .sidebar-link { transition: all 0.3s ease; }
        .sidebar-link:hover { background: linear-gradient(90deg, rgba(109, 33, 33, 0.4) 0%, transparent 100%); border-right: 3px solid #6D2121; }
        .sidebar-link.active { background: linear-gradient(90deg, rgba(109, 33, 33, 0.6) 0%, transparent 100%); border-right: 3px solid #8B2D2D; font-weight: 600; }
        .footer { background-color: #000000; border-top: 1px solid rgba(255, 255, 255, 0.1); padding: 2rem 2.5rem; flex-shrink: 0; }
        .footer-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 2rem; }
        .footer-brand { display: flex; align-items: center; gap: 1rem; }
        .footer-brand img { height: 3rem; width: auto; object-fit: contain; }
        .footer-brand p { font-size: 0.875rem; color: #9ca3af; margin: 0; line-height: 1.5; }
        .footer-brand span { color: #dc2626; font-weight: 600; }
        .footer-social { display: flex; gap: 1rem; }
        .footer-social a { width: 2.5rem; height: 2.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; transition: all 0.3s ease; }
        .footer-social a:nth-child(1) { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .footer-social a:nth-child(1):hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4); }
        .footer-social a:nth-child(2) { background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); }
        .footer-social a:nth-child(2):hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(220, 39, 67, 0.4); }
        .footer-social a:nth-child(3) { background: linear-gradient(135deg, #25d366 0%, #128c7e 100%); }
        .footer-social a:nth-child(3):hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4); }
        @media (max-width: 768px) { .footer-container { flex-direction: column; text-align: center; } .footer-brand { flex-direction: column; } }
        .upload-zone { border: 2px dashed rgba(255, 255, 255, 0.3); transition: all 0.3s ease; }
        .upload-zone:hover { border-color: #dc2626; background-color: rgba(220, 38, 38, 0.1); }
        .upload-zone.dragover { border-color: #dc2626; background-color: rgba(220, 38, 38, 0.2); }
    </style>
    
    @stack('styles')
    <!-- Alpine.js para dropdown, notificaciones y sidebar móvil -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="main-gradient min-h-screen" x-data="{ sidebarOpen: false }">
    
    <!-- CONTAINER PRINCIPAL -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- OVERLAY PARA CERRAR SIDEBAR EN MÓVIL -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
             x-transition:enter="transition-opacity ease-in-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in-out duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak>
        </div>
        
        <!-- ============ SIDEBAR ============ -->
        <aside 
            class="w-64 sidebar-gradient shadow-lg flex-shrink-0 z-50 
                   fixed inset-y-0 left-0 
                   transform transition-transform duration-300 ease-in-out
                   md:translate-x-0 md:static md:inset-0
                   -translate-x-full md:-translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
            
            <!-- Navbar Brand con logos -->
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
            
            <!-- Navegación principal -->
            <nav class="mt-6 px-2">
                <a href="{{ route('estudiante.dashboard') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('estudiante.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home w-6"></i><span>INICIO</span>
                </a>
                <a href="{{ route('estudiante.tareas') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('estudiante.tareas') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag w-6"></i><span>TAREAS</span>
                </a>
            </nav>
            
            <!-- BOTÓN CERRAR SESIÓN -->
            <div class="absolute bottom-4 left-4 right-4">
               <form action="{{ route('logout') }}" method="POST" onsubmit="markUserLoggedOut()">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-red-900 bg-opacity-70 hover:bg-opacity-90 text-white rounded-lg transition border border-red-700 shadow-lg">
                        <i class="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ============ ÁREA DE CONTENIDO PRINCIPAL ============ -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- HEADER / NAVBAR -->
            <header class="bg-black shadow-sm border-b border-gray-800 flex-shrink-0">
                <div class="flex items-center justify-between px-6 py-4">
                    
                    <!-- Título de página -->
                    <div class="flex items-center">
                        <!-- ✅ BOTÓN HAMBURGUESA CON FUNCIONALIDAD -->
                        <button @click="sidebarOpen = !sidebarOpen" 
                                class="md:hidden text-white focus:outline-none mr-4">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>
                        <h2 class="text-xl font-semibold text-white">@yield('page-title', 'Dashboard')</h2>
                    </div>
                    
                    <!-- Acciones del header -->
                    <div class="flex items-center space-x-4">
                        
                        <!-- COMPONENTE DE NOTIFICACIONES -->
                        @include('partials.notification-bell')
                        
                        <!-- DROPDOWN DE PERFIL -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false"
                                    class="flex items-center space-x-3 focus:outline-none cursor-pointer">
                                <div class="text-right hidden sm:block">
                                    <p class="text-sm font-medium text-white">{{ auth()->user()->nombres ?? 'Estudiante' }}</p>
                                    <p class="text-xs text-gray-400">Estudiante</p>
                                </div>
                                <div class="h-10 w-10 rounded-full bg-red-700 flex items-center justify-center text-white font-semibold hover:bg-red-600 transition cursor-pointer">
                                    {{ substr(auth()->user()->nombres ?? 'E', 0, 1) }}
                                </div>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-black bg-opacity-95 border border-gray-700 rounded-lg shadow-xl z-50 overflow-hidden"
                                 x-cloak>
                                
                                <a href="{{ route('profile.show') }}" 
                                   class="block px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 transition flex items-center">
                                    <i class="fas fa-user-circle mr-2"></i>Mi Perfil
                                </a>
                                
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full text-left px-4 py-3 text-red-400 hover:bg-white hover:bg-opacity-10 transition flex items-center">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </header>

            <!-- CONTENIDO SCROLLABLE -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
                
                <!-- Mensajes de sesión -->
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
                
                <!-- Contenido de la vista -->
                @yield('content')
                
            </main>

            <!-- ===== FOOTER ===== -->
            <footer class="footer">
                <div class="footer-container">
                    <div class="footer-brand">
                        <img src="{{ asset('img/logo_univalle_footer.png') }}" alt="Universidad del Valle">
                        <p>
                            DocGest · <span>Universidad del Valle</span><br>
                            <span style="font-size: 0.75rem; color: #6b7280;">Sistema de Gestión Académica</span>
                        </p>
                    </div>

                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </footer>
            
        </div>
    </div>

    @stack('scripts')
</body>
</html>