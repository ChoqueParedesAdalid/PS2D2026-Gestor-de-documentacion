<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel del Estudiante - DocGest Univalle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #120607;
            --bg-secondary: #1A0A0A;
            --bg-panel: #220B0B;
            --sidebar-base: #1E0A0A;
            --sidebar-deep: #4A1010;
            --red-primary: #6B1A1A;
            --red-active: #7A1E1E;
            --red-hover: #8B2A2A;
            --red-button: #B3261E;
            --text-primary: #FFFFFF;
            --text-secondary: #C7C7C7;
            --text-muted: #8A8A8A;
            --border-soft: rgba(255,255,255,0.08);
            --border-table: rgba(255,255,255,0.05);
            --hover-subtle: rgba(255,255,255,0.02);
            --status-pending: #F2C94C;
            --status-approved: #2ECC71;
            --status-info: #56CCF2;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-primary); color: var(--text-primary); min-height: 100vh; }
        
        .sidebar { position: fixed; top: 0; left: 0; width: 280px; height: 100vh; background: linear-gradient(180deg, var(--sidebar-base) 0%, var(--sidebar-deep) 100%); z-index: 1040; display: flex; flex-direction: column; border-right: 1px solid var(--border-soft); }
        .sidebar-header { padding: 24px 20px; border-bottom: 1px solid var(--border-soft); }
        .sidebar-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; color: var(--text-primary); }
        .sidebar-brand:hover { color: var(--text-primary); }
        .brand-icon { width: 45px; height: 45px; background: rgba(107, 26, 26, 0.4); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .brand-text h4 { font-size: 1.1rem; font-weight: 700; margin: 0; }
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
        .btn-logout { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px; background: rgba(179, 38, 30, 0.15); color: var(--text-secondary); border: 1px solid var(--red-primary); border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.2s ease; }
        .btn-logout:hover { background: var(--red-active); color: var(--text-primary); border-color: var(--red-active); }
        
        .hamburger-btn { background: transparent; border: none; color: var(--text-secondary); font-size: 1.3rem; padding: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; }
        .hamburger-btn:hover { color: var(--text-primary); }
        
        .top-navbar { background: var(--bg-secondary); border-bottom: 1px solid var(--border-soft); padding: 0 24px; height: 70px; position: fixed; top: 0; left: 280px; right: 0; z-index: 1030; display: flex; align-items: center; justify-content: space-between; }
        .navbar-left { display: flex; align-items: center; gap: 16px; }
        .navbar-title { font-size: 1.1rem; font-weight: 600; color: var(--text-primary); }
        .navbar-right { display: flex; align-items: center; gap: 20px; }
        .notification-bell { position: relative; cursor: pointer; color: var(--text-muted); font-size: 1.2rem; }
        .notification-badge { position: absolute; top: -6px; right: -6px; background: var(--red-active); color: var(--text-primary); font-size: 0.6rem; padding: 2px 5px; border-radius: 10px; font-weight: 600; }
        .user-profile { display: flex; align-items: center; gap: 12px; }
        .user-info { text-align: right; line-height: 1.2; }
        .user-name { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); }
        .user-role { font-size: 0.7rem; color: var(--text-muted); }
        .user-avatar { width: 38px; height: 38px; background: var(--red-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; color: var(--text-primary); }
        
        .main-content { margin-left: 280px; padding-top: 90px; padding-bottom: 80px; min-height: 100vh; background: var(--bg-primary); }
        .content-wrapper { padding: 0 24px; }
        
        .welcome-card { background: var(--bg-secondary); border: 1px solid var(--border-soft); border-radius: 12px; padding: 24px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; }
        .welcome-info h2 { font-size: 1.5rem; font-weight: 600; margin-bottom: 8px; color: var(--text-primary); }
        .welcome-info p { color: var(--text-muted); margin: 0; font-size: 0.9rem; }
        .user-avatar-large { width: 64px; height: 64px; background: var(--red-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; color: var(--text-primary); }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: var(--bg-secondary); border: 1px solid var(--border-soft); border-radius: 12px; padding: 24px; border-left: 4px solid; }
        .stat-card.pending { border-left-color: var(--status-pending); }
        .stat-card.observed { border-left-color: var(--status-pending); }
        .stat-card.approved { border-left-color: var(--status-approved); }
        .stat-value { font-size: 2rem; font-weight: 700; margin-bottom: 4px; color: var(--text-primary); }
        .stat-label { color: var(--text-muted); font-size: 0.9rem; }
        
        .section-card { background: var(--bg-secondary); border: 1px solid var(--border-soft); border-radius: 12px; overflow: hidden; }
        .section-header { padding: 20px 24px; border-bottom: 1px solid var(--border-soft); display: flex; align-items: center; gap: 10px; }
        .section-header h3 { font-size: 1.1rem; font-weight: 600; margin: 0; color: var(--text-primary); }
        .section-body { padding: 24px; }
        .task-item { padding: 16px; border-bottom: 1px solid var(--border-table); }
        .task-item:hover { background: var(--hover-subtle); }
        .btn-view { background: var(--red-primary); color: var(--text-secondary); border: 1px solid var(--border-soft); padding: 6px 16px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; transition: all 0.2s ease; }
        .btn-view:hover { background: var(--red-active); color: var(--text-primary); }
        .empty-state { text-align: center; padding: 48px 24px; color: var(--text-muted); }
        .empty-state i { font-size: 3rem; opacity: 0.3; margin-bottom: 16px; }
        
        .footer { position: fixed; bottom: 0; left: 280px; right: 0; height: 70px; background: var(--bg-secondary); border-top: 1px solid var(--border-soft); z-index: 1020; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; }
        .footer-brand { display: flex; align-items: center; gap: 12px; }
        .footer-logo { width: 40px; height: 40px; border-radius: 50%; border: 2px solid var(--border-soft); display: flex; align-items: center; justify-content: center; background: var(--bg-panel); }
        .footer-text { color: var(--text-muted); font-size: 0.85rem; }
        .footer-text strong { color: var(--red-active); }
        .social-links { display: flex; gap: 12px; }
        .social-icon { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-secondary); text-decoration: none; transition: all 0.2s ease; }
        .social-icon:hover { transform: scale(1.1); color: var(--text-primary); }
        .social-icon.facebook { background: #3b5998; }
        .social-icon.instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
        .social-icon.whatsapp { background: #25d366; }
        
        @media (max-width: 992px) { .sidebar { transform: translateX(-100%); } .sidebar.show { transform: translateX(0); } .top-navbar, .main-content, .footer { left: 0; } }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header"><a href="#" class="sidebar-brand"><div class="brand-icon"><i class="fas fa-file-invoice"></i></div><div class="brand-text"><h4>DocGest · Univalle</h4><p>Sistema de Gestión Académica</p></div></a></div>
        <div class="univalle-section"><i class="fas fa-university"></i><span style="color: var(--text-secondary);">UNIVALLE<br><small style="opacity: 0.6;">Universidad del Valle</small></span></div>
        <nav class="sidebar-menu">
            <div class="nav-item"><a href="{{ route('estudiante.dashboard') }}" class="nav-link {{ request()->routeIs('estudiante.dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i><span>Inicio</span></a></div>
            <div class="nav-item"><a href="{{ route('estudiante.documentos') }}" class="nav-link {{ request()->routeIs('estudiante.documentos') ? 'active' : '' }}"><i class="fas fa-folder-open"></i><span>Mis Documentos</span></a></div>
        </nav>
        <div class="sidebar-footer"><a href="{{ route('logout') }}" class="btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span></a><form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form></div>
    </aside>

    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="hamburger-btn" onclick="document.getElementById('sidebar').classList.toggle('show')"><i class="fas fa-bars"></i></button>
            <div class="navbar-title">Panel del Estudiante</div>
        </div>
        <div class="navbar-right">
            <div class="notification-bell"><i class="fas fa-bell"></i>@if(($notificaciones ?? collect())->count() > 0)<span class="notification-badge">{{ $notificaciones->count() }}</span>@endif</div>
            <div class="user-profile"><div class="user-info"><div class="user-name">{{ auth()->user()->nombres ?? 'Estudiante' }}</div><div class="user-role">Estudiante</div></div><div class="user-avatar">{{ substr(auth()->user()->nombres ?? 'E', 0, 1) }}</div></div>
        </div>
    </nav>

    <main class="main-content"><div class="content-wrapper">
        @if(!$inscripcion)
            <div class="welcome-card text-center"><div class="w-100"><i class="fas fa-exclamation-triangle fa-3x mb-3" style="color: var(--status-pending); opacity: 0.6;"></i><h3 style="color: var(--text-primary);">Sin inscripción activa</h3><p style="color: var(--text-muted);">Contacta al director de carrera</p></div></div>
        @else
            <div class="welcome-card"><div class="welcome-info"><h2>👋 Bienvenido, {{ $inscripcion->estudiante->nombres ?? 'Estudiante' }}</h2><p>Materia: {{ $inscripcion->materia->nombre ?? 'N/A' }}</p></div><div class="user-avatar-large">{{ substr($inscripcion->estudiante->nombres ?? 'E', 0, 1) }}</div></div>
            <div class="stats-grid">
                <div class="stat-card pending"><div class="stat-value">{{ $tareasPendientes ?? 0 }}</div><div class="stat-label">Pendientes</div></div>
                <div class="stat-card observed"><div class="stat-value">{{ $tareasObservadas ?? 0 }}</div><div class="stat-label">Con Observaciones</div></div>
                <div class="stat-card approved"><div class="stat-value">{{ $tareasAprobadas ?? 0 }}</div><div class="stat-label">Aprobadas</div></div>
            </div>
            <div class="section-card">
                <div class="section-header"><i class="fas fa-tasks" style="color: var(--red-active);"></i><h3>Mis Tareas</h3></div>
                <div class="section-body">
                    @forelse($tareasConInfo ?? [] as $tarea)
                        <div class="task-item"><div style="display: flex; justify-content: space-between; align-items: start;"><div style="flex: 1;"><h5 style="margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">{{ $tarea['titulo'] ?? 'Sin título' }}</h5><p style="color: var(--text-muted); font-size: 0.9rem; margin: 0 0 8px 0;">{{ $tarea['descripcion'] ?? '' }}</p><small style="color: var(--text-muted);"><i class="fas fa-calendar me-2"></i>{{ \Carbon\Carbon::parse($tarea['fecha_limite'])->format('d/m/Y') }}</small></div><a href="{{ route('estudiante.tarea.ver', $tarea['id'] ?? '#') }}" class="btn-view">Ver</a></div></div>
                    @empty
                        <div class="empty-state"><i class="fas fa-clipboard-check"></i><p style="margin: 0; color: var(--text-muted);">No hay tareas asignadas</p></div>
                    @endforelse
                </div>
            </div>
        @endif
    </div></main>

    <footer class="footer">
        <div class="footer-brand"><div class="footer-logo"><i class="fas fa-university" style="color: var(--red-active);"></i></div><div class="footer-text">DocGest · <strong>Universidad del Valle</strong><br><small>Sistema de Gestión Académica</small></div></div>
        <div class="social-links"><a href="#" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a><a href="#" class="social-icon instagram"><i class="fab fa-instagram"></i></a><a href="#" class="social-icon whatsapp"><i class="fab fa-whatsapp"></i></a></div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>