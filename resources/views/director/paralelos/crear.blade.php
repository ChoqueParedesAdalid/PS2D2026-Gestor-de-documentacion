<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crear Paralelo - DocGest Univalle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #120607; --bg-secondary: #1A0A0A; --bg-panel: #220B0B; --sidebar-base: #1E0A0A; --sidebar-deep: #4A1010; --red-primary: #6B1A1A; --red-active: #7A1E1E; --red-hover: #8B2A2A; --red-button: #B3261E; --text-primary: #FFFFFF; --text-secondary: #C7C7C7; --text-muted: #8A8A8A; --border-soft: rgba(255,255,255,0.08); --border-table: rgba(255,255,255,0.05); --hover-subtle: rgba(255,255,255,0.02); --status-pending: #F2C94C; --status-approved: #2ECC71; --status-info: #56CCF2; --status-purple: #9B59B6;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-primary); color: var(--text-primary); min-height: 100vh; }
        .sidebar { position: fixed; top: 0; left: 0; width: 280px; height: 100vh; background: linear-gradient(180deg, var(--sidebar-base) 0%, var(--sidebar-deep) 100%); z-index: 1040; display: flex; flex-direction: column; border-right: 1px solid var(--border-soft); }
        .sidebar-header { padding: 20px 20px; border-bottom: 1px solid var(--border-soft); }
        .sidebar-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; color: var(--text-primary); }
        .brand-icon { width: 40px; height: 40px; background: var(--red-active); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .brand-text h4 { font-size: 1rem; font-weight: 700; margin: 0; }
        .brand-text p { font-size: 0.7rem; margin: 0; color: var(--text-secondary); opacity: 0.7; }
        .univalle-section { padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; align-items: center; gap: 10px; }
        .univalle-section i { font-size: 1.5rem; color: var(--text-muted); opacity: 0.6; }
        .sidebar-menu { flex: 1; padding: 20px 12px; }
        .nav-item { margin-bottom: 4px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: var(--text-secondary); text-decoration: none; border-radius: 8px; transition: all 0.2s ease; font-size: 0.9rem; font-weight: 500; }
        .nav-link:hover { background: var(--hover-subtle); color: var(--text-primary); }
        .nav-link.active { background: var(--red-active); color: var(--text-primary); }
        .nav-link i { width: 20px; text-align: center; font-size: 1rem; }
        .sidebar-footer { padding: 20px; border-top: 1px solid var(--border-soft); }
        .btn-logout { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px; background: rgba(179, 38, 30, 0.15); color: var(--text-secondary); border: 1px solid var(--red-primary); border-radius: 8px; text-decoration: none; font-weight: 500; }
        .btn-logout:hover { background: var(--red-active); color: var(--text-primary); }
        .hamburger-btn { background: transparent; border: none; color: var(--text-secondary); font-size: 1.3rem; padding: 8px; cursor: pointer; }
        .hamburger-btn:hover { color: var(--text-primary); }
        .top-navbar { background: var(--bg-secondary); border-bottom: 1px solid var(--border-soft); padding: 0 24px; height: 70px; position: fixed; top: 0; left: 280px; right: 0; z-index: 1030; display: flex; align-items: center; justify-content: space-between; }
        .navbar-left { display: flex; align-items: center; gap: 16px; }
        .navbar-title { font-size: 1.1rem; font-weight: 600; color: var(--text-primary); }
        .user-avatar { width: 38px; height: 38px; background: var(--red-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; color: var(--text-primary); }
        .main-content { margin-left: 280px; padding-top: 90px; padding-bottom: 80px; min-height: 100vh; background: var(--bg-primary); }
        .content-wrapper { padding: 0 24px; }
        
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .page-header h2 { font-size: 1.3rem; font-weight: 600; color: var(--text-primary); }
        .page-header p { color: var(--text-muted); margin: 0; font-size: 0.9rem; }
        .btn-back { background: transparent; color: var(--text-secondary); border: 1px solid var(--border-soft); padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.85rem; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 8px; }
        .btn-back:hover { background: var(--hover-subtle); color: var(--text-primary); }
        
        .form-card { background: var(--bg-secondary); border: 1px solid var(--border-soft); border-radius: 12px; overflow: hidden; max-width: 700px; margin: 0 auto; }
        .form-header { padding: 20px 24px; border-bottom: 1px solid var(--border-soft); display: flex; align-items: center; gap: 10px; }
        .form-header h3 { font-size: 1rem; font-weight: 600; margin: 0; color: var(--text-primary); }
        .form-header i { color: var(--red-active); }
        .form-body { padding: 24px; }
        
        .form-label { font-weight: 500; margin-bottom: 8px; color: var(--text-secondary); font-size: 0.9rem; }
        .form-label .required { color: var(--red-button); }
        .form-control, .form-select { background: var(--bg-panel); border: 1px solid var(--border-soft); color: var(--text-primary); border-radius: 8px; padding: 10px 14px; font-size: 0.9rem; }
        .form-control:focus, .form-select:focus { background: var(--bg-panel); border-color: var(--red-active); color: var(--text-primary); box-shadow: 0 0 0 3px rgba(122, 30, 30, 0.2); }
        .form-control::placeholder { color: var(--text-muted); }
        .form-text { color: var(--text-muted); font-size: 0.8rem; margin-top: 6px; }
        .form-check-label { color: var(--text-secondary); font-size: 0.9rem; }
        
        .form-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-soft); }
        .btn-cancel { background: transparent; color: var(--text-secondary); border: 1px solid var(--border-soft); padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.2s ease; }
        .btn-cancel:hover { background: var(--hover-subtle); color: var(--text-primary); }
        .btn-save { background: var(--red-button); color: var(--text-primary); border: none; padding: 10px 24px; border-radius: 8px; font-weight: 500; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 8px; }
        .btn-save:hover { background: var(--red-active); }
        
        .footer { position: fixed; bottom: 0; left: 280px; right: 0; height: 70px; background: var(--bg-secondary); border-top: 1px solid var(--border-soft); z-index: 1020; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; }
        .footer-brand { display: flex; align-items: center; gap: 12px; }
        .footer-logo { width: 40px; height: 40px; border-radius: 50%; border: 2px solid var(--border-soft); display: flex; align-items: center; justify-content: center; background: var(--bg-panel); }
        .footer-logo i { color: var(--red-active); }
        .footer-text { color: var(--text-muted); font-size: 0.85rem; }
        .footer-text strong { color: var(--red-active); }
        .social-links { display: flex; gap: 12px; }
        .social-icon { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-secondary); text-decoration: none; }
        .social-icon.facebook { background: #3b5998; }
        .social-icon.instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
        .social-icon.whatsapp { background: #25d366; }
        
        @media (max-width: 992px) { .sidebar { transform: translateX(-100%); } .sidebar.show { transform: translateX(0); } .top-navbar, .main-content, .footer { left: 0; } }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header"><a href="#" class="sidebar-brand"><div class="brand-icon"><i class="fas fa-file-invoice"></i></div><div class="brand-text"><h4>DocGest · Univalle</h4><p>Sistema de Gestión Académica</p></div></a></div>
        <div class="univalle-section"><i class="fas fa-university"></i><span>UNIVALLE<br><small>Universidad del Valle</small></span></div>
        <nav class="sidebar-menu">
            <div class="nav-item"><a href="{{ route('director.dashboard') }}" class="nav-link"><i class="fas fa-home"></i><span>Inicio</span></a></div>
            <div class="nav-item"><a href="{{ route('director.paralelos') }}" class="nav-link {{ request()->routeIs('director.paralelos*') ? 'active' : '' }}"><i class="fas fa-layer-group"></i><span>Paralelos</span></a></div>
            <div class="nav-item"><a href="{{ route('director.docentes') }}" class="nav-link"><i class="fas fa-chalkboard-teacher"></i><span>Docentes</span></a></div>
            <div class="nav-item"><a href="{{ route('director.estudiantes') }}" class="nav-link"><i class="fas fa-user-graduate"></i><span>Estudiantes</span></a></div>
            <div class="nav-item"><a href="#" class="nav-link"><i class="fas fa-folder-open"></i><span>Documentos</span></a></div>
        </nav>
        <div class="sidebar-footer"><a href="{{ route('logout') }}" class="btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span></a><form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form></div>
    </aside>

    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="hamburger-btn" onclick="document.getElementById('sidebar').classList.toggle('show')"><i class="fas fa-bars"></i></button>
            <div class="navbar-title">Crear Nuevo Paralelo</div>
        </div>
        <div class="user-avatar">{{ substr(Auth::user()->nombres ?? 'D', 0, 1) }}</div>
    </nav>

    <main class="main-content"><div class="content-wrapper">
        
        @if($errors->any())<div class="alert alert-dismissible fade show mb-4" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #ef4444; border-radius: 8px;"><ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul><button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button></div>@endif

        <div class="page-header">
            <div><h2>Información del Paralelo</h2><p>Completa los datos para crear un nuevo paralelo académico</p></div>
            <a href="{{ route('director.paralelos') }}" class="btn-back"><i class="fas fa-arrow-left"></i>Volver</a>
        </div>

        <div class="form-card">
            <div class="form-header"><i class="fas fa-plus-circle"></i><h3>Crear Paralelo</h3></div>
            <div class="form-body">
                <form action="{{ route('director.paralelos.guardar') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label">Gestión Académica <span class="required">*</span></label>
                        <select name="gestion_id" class="form-select" required>
                            <option value="">Seleccione...</option>
                            @forelse($gestiones ?? [] as $gestion)
                                <option value="{{ $gestion->id }}" {{ old('gestion_id') == $gestion->id ? 'selected' : '' }}>{{ $gestion->nombre }}</option>
                            @empty
                                <option disabled>No hay gestiones activas</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Materia <span class="required">*</span></label>
                            <select name="materia_id" class="form-select" required>
                                <option value="">Seleccione...</option>
                                @forelse($materias ?? [] as $materia)
                                    <option value="{{ $materia->id }}" {{ old('materia_id') == $materia->id ? 'selected' : '' }}>{{ $materia->nombre }}</option>
                                @empty
                                    <option disabled>No hay materias registradas</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Letra del Paralelo <span class="required">*</span></label>
                            <select name="letra" class="form-select" required>
                                <option value="">Seleccione...</option>
                                @foreach(['A','B','C','D','E','F','G','H'] as $letra)
                                    <option value="{{ $letra }}" {{ old('letra') == $letra ? 'selected' : '' }}>{{ $letra }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Docente a Cargo</label>
                        <select name="docente_cargo_id" class="form-select">
                            <option value="">-- Sin asignar --</option>
                            @forelse($docentes ?? [] as $docente)
                                <option value="{{ $docente->id }}" {{ old('docente_cargo_id') == $docente->id ? 'selected' : '' }}>{{ $docente->nombres }} {{ $docente->apellidos }} ({{ $docente->email_institucional }})</option>
                            @empty
                                <option disabled>No hay docentes disponibles</option>
                            @endforelse
                        </select>
                        <small class="form-text"><i class="fas fa-info-circle me-1"></i>Un docente puede tener máximo 2 paralelos activos</small>
                        @error('docente_cargo_id')<div style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Fecha de Inicio <span class="required">*</span></label>
                            <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio') }}" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Fecha de Fin <span class="required">*</span></label>
                            <input type="date" name="fecha_fin" class="form-control" value="{{ old('fecha_fin') }}" required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('director.paralelos') }}" class="btn-cancel">Cancelar</a>
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i>Guardar Paralelo</button>
                    </div>
                </form>
            </div>
        </div>
    </div></main>

    <footer class="footer"><div class="footer-brand"><div class="footer-logo"><i class="fas fa-university"></i></div><div class="footer-text">DocGest · <strong>Universidad del Valle</strong><br><small>Sistema de Gestión Académica</small></div></div><div class="social-links"><a href="#" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a><a href="#" class="social-icon instagram"><i class="fab fa-instagram"></i></a><a href="#" class="social-icon whatsapp"><i class="fab fa-whatsapp"></i></a></div></footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>