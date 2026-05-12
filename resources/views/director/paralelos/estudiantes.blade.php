<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Estudiantes - Paralelo {{ $paralelo->letra }} - DocGest Univalle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg-primary: #120607; --bg-secondary: #1A0A0A; --bg-panel: #220B0B; --sidebar-base: #1E0A0A; --sidebar-deep: #4A1010; --red-primary: #6B1A1A; --red-active: #7A1E1E; --red-hover: #8B2A2A; --red-button: #B3261E; --text-primary: #FFFFFF; --text-secondary: #C7C7C7; --text-muted: #8A8A8A; --border-soft: rgba(255,255,255,0.08); --border-table: rgba(255,255,255,0.05); --hover-subtle: rgba(255,255,255,0.02); --status-pending: #F2C94C; --status-approved: #2ECC71; --status-info: #56CCF2; --status-purple: #9B59B6; }
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
        
        .info-banner { background: var(--bg-secondary); border: 1px solid var(--border-soft); border-left: 4px solid var(--red-active); border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .info-banner h3 { font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; color: var(--text-primary); }
        .info-banner p { color: var(--text-muted); margin: 0; font-size: 0.9rem; }
        
        .section-card { background: var(--bg-secondary); border: 1px solid var(--border-soft); border-radius: 12px; overflow: hidden; }
        .section-header { padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; align-items: center; gap: 8px; }
        .section-header h3 { font-size: 1rem; font-weight: 600; margin: 0; color: var(--text-primary); }
        .section-header i { color: var(--red-active); }
        .section-body { padding: 0; }
        .table { color: var(--text-primary); margin-bottom: 0; }
        .table thead th { background: rgba(255,255,255,0.03); border-bottom: 1px solid var(--border-soft); color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; padding: 12px 16px; font-weight: 600; border-top: none; letter-spacing: 0.5px; }
        .table tbody td { border-bottom: 1px solid var(--border-table); padding: 14px 16px; vertical-align: middle; }
        .table tbody tr:hover { background: var(--hover-subtle); }
        .badge-activo { background: rgba(46, 204, 113, 0.15); color: var(--status-approved); padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-inactivo { background: rgba(239, 68, 68, 0.15); color: #ef4444; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        
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
        </nav>
        <div class="sidebar-footer"><a href="{{ route('logout') }}" class="btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span></a><form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form></div>
    </aside>

    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="hamburger-btn" onclick="document.getElementById('sidebar').classList.toggle('show')"><i class="fas fa-bars"></i></button>
            <div class="navbar-title">Estudiantes - Paralelo {{ $paralelo->letra }}</div>
        </div>
        <div class="user-avatar">{{ substr(Auth::user()->nombres ?? 'D', 0, 1) }}</div>
    </nav>

    <main class="main-content"><div class="content-wrapper">
        
        <div class="page-header">
            <div>
                <h2>Estudiantes del Paralelo {{ $paralelo->letra }}</h2>
                <p>{{ $paralelo->materia?->nombre ?? 'Sin materia' }} · {{ $paralelo->gestion?->nombre ?? 'N/A' }}</p>
            </div>
            <a href="{{ route('director.paralelos.detalle', $paralelo->id) }}" class="btn-back"><i class="fas fa-arrow-left"></i> Volver al Paralelo</a>
        </div>

        <div class="info-banner">
            <h3><i class="fas fa-info-circle me-2" style="color: var(--red-active);"></i>Información del Paralelo</h3>
            <p>
                <strong>Materia:</strong> {{ $paralelo->materia?->nombre ?? 'N/A' }} · 
                <strong>Docente:</strong> {{ $paralelo->docenteCargo ? $paralelo->docenteCargo->nombres . ' ' . $paralelo->docenteCargo->apellidos : 'Sin asignar' }} · 
                <strong>Total Estudiantes:</strong> {{ $paralelo->estudiantes->count() }}
            </p>
        </div>

        <div class="section-card">
            <div class="section-header"><h3><i class="fas fa-users"></i>Lista de Estudiantes Inscritos</h3></div>
            <div class="section-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre Completo</th>
                                <th>Email Institucional</th>
                                <th>CI</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paralelo->estudiantes as $index => $estudiante)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $estudiante->nombres }} {{ $estudiante->apellidos }}</strong></td>
                                    <td style="color: var(--text-secondary);">{{ $estudiante->email_institucional }}</td>
                                    <td>{{ $estudiante->ci }}</td>
                                    <td style="color: var(--text-secondary);">{{ $estudiante->telefono ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge-{{ $estudiante->pivot->estado_inscripcion === 'activo' ? 'activo' : 'inactivo' }}">
                                            {{ ucfirst($estudiante->pivot->estado_inscripcion) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5" style="color: var(--text-muted);">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block opacity-25"></i>
                                        No hay estudiantes inscritos en este paralelo
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div></main>

    <footer class="footer"><div class="footer-brand"><div class="footer-logo"><i class="fas fa-university"></i></div><div class="footer-text">DocGest · <strong>Universidad del Valle</strong><br><small>Sistema de Gestión Académica</small></div></div><div class="social-links"><a href="#" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a><a href="#" class="social-icon instagram"><i class="fab fa-instagram"></i></a><a href="#" class="social-icon whatsapp"><i class="fab fa-whatsapp"></i></a></div></footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>