<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DocGest - Sistema de Gestión Documental para Proyectos de Grado - Universidad del Valle">
    <title>{{ $appName }} - Gestión de Documentación de Proyectos</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/png">
    
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Iconos (Font Awesome - CDN gratuito) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- NAVBAR -->
        <nav class="navbar">
        <div class="navbar-content">
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

            <!-- Botón Hamburguesa (solo visible en móvil) -->
            <button class="navbar-toggle" aria-label="Abrir menú" onclick="toggleMenu()">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>

            <ul class="navbar-nav" id="navMenu">
                <li><a href="#inicio" onclick="closeMenu()"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="#caracteristicas" onclick="closeMenu()"><i class="fas fa-chart-bar"></i> Características</a></li>
                <li><a href="#flujo" onclick="closeMenu()"><i class="fas fa-cogs"></i> Flujo de Trabajo</a></li>
                <li><a href="#roles" onclick="closeMenu()"><i class="fas fa-users"></i> Roles</a></li>
                <li><a href="{{ route('login') }}" class="btn-navbar-login" onclick="closeMenu()"><i class="fas fa-sign-in-alt"></i> Ingresar</a></li>
            </ul>
        </div>
    </nav>

    <!-- ===== HERO SECTION ===== -->
    <section id="inicio" class="hero">
        <div class="hero-background">
            <img src="{{ asset('img/background_office.png') }}" alt="Background Office">
            <div class="hero-overlay"></div>
        </div>

        <div class="hero-content">
            <div class="hero-badge">
                • Universidad del Valle - Bolivia
            </div>

            <h1 class="hero-title">
                Gestión de Documentación<br>
                <span class="hero-title-bold">de Proyectos</span>
            </h1>

            <p class="hero-description">
                Centraliza la revisión de documentos, facilita la comunicación entre estudiantes, 
                tutores y revisores, y avanza con claridad hacia tu titulación.
            </p>

            <a href="{{ route('login') }}" class="btn-hero-access">
                <i class="fas fa-arrow-right-to-bracket"></i> ACCEDER AL SISTEMA
            </a>
        </div>
    </section>

    <!-- ===== FEATURES SECTION ===== -->
    <section id="caracteristicas" class="features">
        <div class="features-container">
            <div class="features-header">
                <h2>Todo lo que necesitas, en un solo lugar.</h2>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="{{ asset('img/icons/etapas.png') }}" alt="Gestión de proyectos">
                    </div>
                    <h3>Gestión de proyectos por etapas</h3>
                    <p>Organiza tu proyecto de grado en fases claras y definidas con seguimiento continuo.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="{{ asset('img/icons/upload.png') }}" alt="Carga segura">
                    </div>
                    <h3>Carga segura de PDF / DOCX</h3>
                    <p>Sube tus documentos con versionado automático y almacenamiento seguro en la nube.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="{{ asset('img/icons/search-doc.png') }}" alt="Observaciones">
                    </div>
                    <h3>Observaciones por secciones</h3>
                    <p>Recibe feedback detallado organizado por capítulos y secciones de tu documento.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="{{ asset('img/icons/bell.png') }}" alt="Notificaciones">
                    </div>
                    <h3>Notificaciones y avisos de actividades</h3>
                    <p>Mantente informado sobre revisiones, aprobaciones y fechas límite importantes.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== ROLES SECTION ===== -->
    <section id="roles" class="roles">
        <div class="roles-content">
            <div class="roles-info">
                <div class="roles-container">
                    <div class="roles-header">
                        <span class="roles-label">Roles del sistema</span>
                        <h2>Cada perfil, espacio propio.</h2>
                    </div>

                    <div class="roles-grid">
                        <div class="role-card">
                            <img src="{{ asset('img/icons/director.png') }}" alt="Director">
                            <h3>Director Docente</h3>
                            <p>Acceso total como administrador central del sistema.</p>
                            <ul>
                                <li>Ver todos los paralelos</li>
                                <li>Asignar docentes a cargo</li>
                            </ul>
                        </div>

                        <div class="role-card">
                            <img src="{{ asset('img/icons/docente.png') }}" alt="Docente">
                            <h3>Docente a Cargo</h3>
                            <p>Gestiona su paralelo, tareas y asignaciones.</p>
                            <ul>
                                <li>Agregar estudiantes</li>
                                <li>Crear tareas de revisión</li>
                                <li>Asignar jurado y tutor</li>
                            </ul>
                        </div>

                        <div class="role-card">
                            <img src="{{ asset('img/icons/profesor.png') }}" alt="Tribunal">
                            <h3>Tribunal/Tutor</h3>
                            <p>Revisa documentos y deja observaciones.</p>
                            <ul>
                                <li>Ver documentos asignados</li>
                                <li>Agregar observaciones</li>
                                <li>Tickear correcciones</li>
                            </ul>
                        </div>

                        <div class="role-card">
                            <img src="{{ asset('img/icons/estudiante.png') }}" alt="Tutor">
                            <h3>Estudiante</h3>
                            <p>Sube documentos esperando su revision. </p>
                            <ul>
                                <li>Ver tareas y entregables</li>
                                <li>Carga documentos</li>
                                <li>Recibe notificaciones</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="roles-image">
                <img src="{{ asset('img/laptop_meeting.jpg') }}" alt="Team meeting">
            </div>
        </div>
    </section>

    <!-- ===== HOW IT WORKS SECTION ===== -->
    <section id="flujo" class="how-it-works">
        <div class="how-container">
            <span class="how-label">¿Cómo funciona?</span>
            <h2>El proceso de documentación, paso a paso.</h2>

            <div class="how-steps">
                <div class="step">
                    <div class="step-number">
                        <span>1</span>
                    </div>
                    <h3>Configuración</h3>
                    <p>Director asigna docente a cargo al paralelo</p>
                </div>

                <div class="step">
                    <div class="step-number">
                        <span>2</span>
                    </div>
                    <h3>Inscripción</h3>
                    <p>Docente agrega estudiantes al paralelo</p>
                </div>

                <div class="step">
                    <div class="step-number">
                        <span>3</span>
                    </div>
                    <h3>Asignación</h3>
                    <p>1 tutor y 2 tribunales por estudiante</p>
                </div>

                <div class="step">
                    <div class="step-number">
                        <span>4</span>
                    </div>
                    <h3>Revisiones</h3>
                    <p>Estudiante sube docs en fechas establecidas</p>
                </div>

                <div class="step">
                    <div class="step-number">
                        <span>5</span>
                    </div>
                    <h3>Aprobación</h3>
                    <p>Tribunales observan, tutor da visto bueno</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA SECTION ===== -->
    <section id="contacto" class="cta">
        <div class="cta-container">
            <span class="cta-label">Listo para empezar</span>
            <h2>Sin papeles. Sin correos.<br><strong>Sin caos.</strong></h2>
            <p>
                Centraliza todo el proceso de gestión documental de tu facultad 
                en un solo sistema claro y organizado.
            </p>
            <button class="btn-cta"><a href="{{ route('login') }}">
                INGRESAR AL SISTEMA <i class="fas fa-arrow-right"></i></a>
            </button>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="footer-container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 2rem;">
            <div class="footer-brand">
                <img src="{{ asset('img/logo_univalle_footer.png') }}" alt="Univalle">
                <p>
                    DocGest · <span>Universidad del Valle</span><br>
                </p>
            </div>

            <div class="footer-social">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </footer>

    <!-- Smooth scroll para navegación -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
        <script>
        function toggleMenu() {
            const nav = document.getElementById('navMenu');
            nav.classList.toggle('active');
        }
        function closeMenu() {
            document.getElementById('navMenu').classList.remove('active');
        }
        // Smooth scroll para anclas
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    </script>
</body>
</html>
</body>
</html>