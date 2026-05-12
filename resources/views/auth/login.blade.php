<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name', 'DocGest') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/png">
    
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-page">

    <!-- ===== NAVBAR SIMPLIFICADO ===== -->
    <nav class="navbar navbar-simple">
        <div class="navbar-content">
            <div class="navbar-brand">
                <div class="brand-logos">
                    <img src="{{ asset('img/logo_docgest.png') }}" alt="DocGest Logo">
                    <div class="brand-text">
                        <h1><a href="{{ route('home') }}">DocGest · Univalle</a></h1>
                        <p>Sistema de Gestión Académica</p>
                    </div>
                </div>
                <span class="brand-separator"></span>
                <div class="brand-univalle">
                    <img src="{{ asset('img/logo_univalle.png') }}" alt="Univalle">
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== LOGIN CONTAINER ===== -->
    <main class="login-main">
        <!-- Background Image -->
        <div class="login-background">
            <img src="{{ asset('img/background-registro.jpg') }}" alt="Univalle Campus">
            <div class="login-overlay"></div>
        </div>

        <!-- Login Form Container -->
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h2>INICIAR SESIÓN</h2>
                    <p>Ingresa tus credenciales para acceder al sistema</p>
                </div>

                <!-- Mostrar errores de validación -->
                @if ($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login.process') }}" method="POST" class="login-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Correo Electrónico
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            placeholder="tu.correo@univalle.edu" 
                            class="form-input" 
                            required 
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Contraseña
                        </label>
                        <div class="password-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="••••••••" 
                                class="form-input" 
                                required
                            >
                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember" id="remember">
                            <span class="checkmark"></span>
                            Recordar sesión
                        </label>
                        <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> INGRESAR AL SISTEMA
                    </button>
                </form>

                <div class="login-footer">
                    <p>¿Problemas para ingresar?</p>
                    <p class="contact-text">
                        Contacta a tu coordinador o docente a cargo<br>
                        <a href="mailto:coordinador@univalle.edu">coordinador@univalle.edu</a>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Script para mostrar/ocultar contraseña -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>