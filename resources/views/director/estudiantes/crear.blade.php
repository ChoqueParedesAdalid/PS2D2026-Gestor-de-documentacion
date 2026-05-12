<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crear Estudiante - DocGest Univalle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg-main: #7B2D32; --bg-card: #2D1520; --bg-sidebar: linear-gradient(180deg, #6B1530 0%, #8B1E3F 100%); }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-main); color: #ffffff; }
        .sidebar { position: fixed; top: 0; left: 0; width: 260px; height: 100vh; background: var(--bg-sidebar); z-index: 1040; display: flex; flex-direction: column; }
        .sidebar-brand { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); text-decoration: none; color: white; display: flex; align-items: center; gap: 12px; }
        .sidebar-brand:hover { color: white; }
        .brand-icon { width: 42px; height: 42px; background: rgba(255,255,255,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
        .sidebar-logo { padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 10px; }
        .sidebar-menu { flex: 1; padding: 16px 12px; }
        .sidebar-menu .nav-link { color: rgba(255,255,255,0.85); padding: 12px 16px; border-radius: 8px; margin-bottom: 4px; font-size: 0.9rem; font-weight: 500; display: flex; align-items: center; gap: 12px; }
        .sidebar-menu .nav-link:hover { background: rgba(255,255,255,0.1); color: white; }
        .sidebar-menu .nav-link.active { background: rgba(255,255,255,0.15); color: white; border-left: 3px solid #e74c3c; }
        .sidebar-footer { padding: 16px 12px; border-top: 1px solid rgba(255,255,255,0.1); }
        .btn-logout { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px; background: rgba(0,0,0,0.3); color: white; border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; text-decoration: none; font-weight: 500; }
        .btn-logout:hover { background: #e74c3c; border-color: #e74c3c; color: white; }
        .navbar-top { background: #1a0a0f; border-bottom: 1px solid rgba(255,255,255,0.1); height: 64px; position: fixed; top: 0; left: 260px; right: 0; z-index: 1030; padding: 0 24px; }
        .main-content { margin-left: 260px; padding-top: 84px; padding-bottom: 70px; min-height: 100vh; }
        .section-card { background: var(--bg-card); border-radius: 12px; overflow: hidden; }
        .section-card .card-header { background: transparent; border-bottom: 1px solid rgba(255,255,255,0.1); padding: 16px 20px; font-weight: 600; }
        .form-label { font-weight: 500; margin-bottom: 8px; }
        .form-control, .form-select { background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: white; }
        .form-control:focus, .form-select:focus { background: rgba(0,0,0,0.4); border-color: #e74c3c; color: white; box-shadow: 0 0 0 0.2rem rgba(231,76,60,0.25); }
        .form-control::placeholder { color: rgba(255,255,255,0.4); }
        .footer { position: fixed; bottom: 0; left: 260px; right: 0; height: 56px; background: #1a0a0f; border-top: 1px solid rgba(255,255,255,0.1); z-index: 1020; }
        @media (max-width: 991.98px) { .sidebar { transform: translateX(-100%); } .sidebar.show { transform: translateX(0); } .navbar-top, .main-content, .footer { left: 0; } }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <a href="#" class="sidebar-brand"><div class="brand-icon"><i class="fas fa-file-invoice"></i></div><div><h5 class="mb-0">DocGest · Univalle</h5><small>Sistema de Gestión Académica</small></div></a>
        <div class="sidebar-logo"><i class="fas fa-university fa-lg opacity-50"></i><span>UNIVALLE<br><small class="opacity-50">Universidad del Valle</small></span></div>
        <nav class="sidebar-menu">
            <a href="{{ route('director.dashboard') }}" class="nav-link"><i class="fas fa-home"></i> Inicio</a>
            <a href="{{ route('director.paralelos') }}" class="nav-link"><i class="fas fa-layer-group"></i> Paralelos</a>
            <a href="{{ route('director.docentes') }}" class="nav-link"><i class="fas fa-chalkboard-teacher"></i> Docentes</a>
            <a href="{{ route('director.estudiantes') }}" class="nav-link active"><i class="fas fa-user-graduate"></i> Estudiantes</a>
        </nav>
        <div class="sidebar-footer"><a href="{{ route('logout') }}" class="btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a><form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form></div>
    </aside>

    <!-- NAVBAR -->
    <nav class="navbar-top d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm text-white d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')"><i class="fas fa-bars"></i></button>
            <h5 class="mb-0 fw-semibold">Registrar Nuevo Estudiante</h5>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('director.estudiantes') }}" class="btn btn-outline-light btn-sm"><i class="fas fa-arrow-left me-1"></i> Volver</a>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="container-fluid px-4">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" style="background: rgba(231,76,60,0.15); border-color: #e74c3c; color: #e74c3c;">
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="section-card">
                        <div class="card-header"><i class="fas fa-user-plus me-2 text-danger"></i>Información del Estudiante</div>
                        <div class="card-body p-4">
                            <form action="{{ route('director.estudiantes.guardar') }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Nombres <span class="text-danger">*</span></label>
                                        <input type="text" name="nombres" class="form-control" value="{{ old('nombres') }}" required placeholder="Ej: Juan Carlos">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                                        <input type="text" name="apellidos" class="form-control" value="{{ old('apellidos') }}" required placeholder="Ej: Pérez García">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Email Institucional <span class="text-danger">*</span></label>
                                    <input type="email" name="email_institucional" class="form-control" value="{{ old('email_institucional') }}" required placeholder="Ej: juan.perez@univalle.edu">
                                    <small class="text-white-50"><i class="fas fa-info-circle me-1"></i>Este será su usuario para ingresar al sistema</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Cédula de Identidad (CI) <span class="text-danger">*</span></label>
                                        <input type="text" name="ci" class="form-control" value="{{ old('ci') }}" required placeholder="Ej: 1234567">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}" placeholder="Ej: 77712345">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control" required minlength="6" placeholder="Mínimo 6 caracteres">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" class="form-control" required minlength="6" placeholder="Repita la contraseña">
                                    </div>
                                </div>

                                <div class="d-flex gap-3 justify-content-end">
                                    <a href="{{ route('director.estudiantes') }}" class="btn btn-outline-light">Cancelar</a>
                                    <button type="submit" class="btn btn-danger px-4"><i class="fas fa-save me-2"></i>Registrar Estudiante</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="footer d-flex align-items-center justify-content-center px-4">
        <small class="text-white-50">DocGest · Universidad del Valle · Sistema de Gestión Académica © 2026</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>