@extends('layouts.director')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Paralelos - DocGest Univalle</title>
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
            --status-purple: #9B59B6;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-primary); color: var(--text-primary); min-height: 100vh; }
        
        .sidebar { position: fixed; top: 0; left: 0; width: 280px; height: 100vh; background: linear-gradient(180deg, var(--sidebar-base) 0%, var(--sidebar-deep) 100%); z-index: 1040; display: flex; flex-direction: column; border-right: 1px solid var(--border-soft); }
        .sidebar-header { padding: 20px 20px; border-bottom: 1px solid var(--border-soft); }
        .sidebar-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; color: var(--text-primary); }
        .sidebar-brand:hover { color: var(--text-primary); }
        .brand-icon { width: 40px; height: 40px; background: var(--red-active); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .brand-text h4 { font-size: 1rem; font-weight: 700; margin: 0; }
        .brand-text p { font-size: 0.7rem; margin: 0; color: var(--text-secondary); opacity: 0.7; }
        .univalle-section { padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; align-items: center; gap: 10px; }
        .univalle-section i { font-size: 1.5rem; color: var(--text-muted); opacity: 0.6; }
        .univalle-section span { color: var(--text-secondary); font-size: 0.85rem; }
        .univalle-section small { opacity: 0.6; color: var(--text-muted); }
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
        .director-badge { display: flex; align-items: center; gap: 8px; padding: 6px 14px; background: rgba(107, 26, 26, 0.3); border: 1px solid var(--red-primary); border-radius: 6px; font-size: 0.8rem; color: var(--text-secondary); }
        .director-badge i { color: var(--red-active); }
        .director-badge .badge-active { background: var(--red-button); color: var(--text-primary); font-size: 0.65rem; padding: 2px 8px; border-radius: 4px; }
        .notification-bell { position: relative; cursor: pointer; color: var(--text-muted); font-size: 1.2rem; }
        .notification-badge { position: absolute; top: -6px; right: -6px; background: var(--red-active); color: var(--text-primary); font-size: 0.6rem; padding: 2px 5px; border-radius: 10px; font-weight: 600; }
        .user-profile { display: flex; align-items: center; gap: 12px; }
        .user-avatar { width: 38px; height: 38px; background: var(--red-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; color: var(--text-primary); }
        .user-info { text-align: right; line-height: 1.2; }
        .user-name { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); }
        .user-role { font-size: 0.7rem; color: var(--text-muted); }
        
        .main-content { margin-left: 280px; padding-top: 90px; padding-bottom: 80px; min-height: 100vh; background: var(--bg-primary); }
        .content-wrapper { padding: 0 24px; }
        
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .page-header h2 { font-size: 1.3rem; font-weight: 600; color: var(--text-primary); }
        .page-header p { color: var(--text-muted); margin: 0; font-size: 0.9rem; }
        
        .btn-create { background: var(--red-button); color: var(--text-primary); border: none; padding: 8px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 8px; }
        .btn-create:hover { background: var(--red-active); color: var(--text-primary); }
        .btn-back { background: transparent; color: var(--text-secondary); border: 1px solid var(--border-soft); padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.85rem; transition: all 0.2s ease; }
        .btn-back:hover { background: var(--hover-subtle); color: var(--text-primary); }
        
        .footer { position: fixed; bottom: 0; left: 280px; right: 0; height: 70px; background: var(--bg-secondary); border-top: 1px solid var(--border-soft); z-index: 1020; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; }
        .footer-brand { display: flex; align-items: center; gap: 12px; }
        .footer-logo { width: 40px; height: 40px; border-radius: 50%; border: 2px solid var(--border-soft); display: flex; align-items: center; justify-content: center; background: var(--bg-panel); }
        .footer-logo i { color: var(--red-active); }
        .footer-text { color: var(--text-muted); font-size: 0.85rem; }
        .footer-text strong { color: var(--red-active); }
        .social-links { display: flex; gap: 12px; }
        .social-icon { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-secondary); text-decoration: none; }
        .social-icon:hover { transform: scale(1.1); color: var(--text-primary); }
        .social-icon.facebook { background: #3b5998; }
        .social-icon.instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
        .social-icon.whatsapp { background: #25d366; }
        
        @media (max-width: 992px) { .sidebar { transform: translateX(-100%); } .sidebar.show { transform: translateX(0); } .top-navbar, .main-content, .footer { left: 0; } }
                /* ===== OVERRIDE BOOTSTRAP 5 - TEMA OSCURO FORZADO ===== */
        .table, .table > :not(caption) > * > *, .table thead th, .table tbody td {
            --bs-table-bg: var(--bg-secondary) !important;
            --bs-table-color: var(--text-primary) !important;
            --bs-table-striped-bg: var(--bg-panel) !important;
            --bs-table-striped-color: var(--text-primary) !important;
            --bs-table-hover-bg: var(--hover-subtle) !important;
            --bs-table-hover-color: var(--text-primary) !important;
            --bs-table-border-color: var(--border-table) !important;
            background-color: var(--bg-secondary) !important;
            color: var(--text-primary) !important;
            border-color: var(--border-table) !important;
        }
        .table thead th {
            background-color: var(--bg-panel) !important;
            color: var(--text-secondary) !important;
            border-bottom: 2px solid var(--red-primary) !important;
        }
        .card, .section-card, .alert, .alert-dismissible, .nav-tabs, .nav-tabs .nav-link, .nav-tabs .nav-link.active, .tab-content, .tab-pane {
            background-color: var(--bg-secondary) !important;
            color: var(--text-primary) !important;
            border-color: var(--border-soft) !important;
        }
        .alert-success {
            background: rgba(46, 204, 113, 0.15) !important;
            border-color: rgba(46, 204, 113, 0.3) !important;
            color: var(--status-approved) !important;
        }
        .form-control, .form-select, .input-group-text {
            --bs-body-bg: var(--bg-panel) !important;
            --bs-body-color: var(--text-primary) !important;
            background-color: var(--bg-panel) !important;
            border: 1px solid var(--border-soft) !important;
            color: var(--text-primary) !important;
        }
        .form-control:focus, .form-select:focus {
            background-color: var(--bg-panel) !important;
            color: var(--text-primary) !important;
            border-color: var(--red-active) !important;
            box-shadow: 0 0 0 0.25rem rgba(122, 30, 30, 0.25) !important;
        }
        .form-control::placeholder { color: var(--text-muted) !important; opacity: 1; }
        .btn-close { filter: invert(1) grayscale(100%) brightness(200%) !important; }
        .nav-tabs .nav-link { color: var(--text-muted) !important; border: none !important; border-bottom: 2px solid transparent !important; }
        .nav-tabs .nav-link:hover { color: var(--text-primary) !important; background: var(--hover-subtle) !important; }
        .nav-tabs .nav-link.active { color: var(--text-primary) !important; background: transparent !important; border-bottom-color: var(--red-active) !important; }

        /* ===== ANIMACIONES ===== */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(25px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.02); } }

        .stat-card, .section-card, .table tbody tr { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; }
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        .table tbody tr:nth-child(1) { animation-delay: 0.1s; }
        .table tbody tr:nth-child(2) { animation-delay: 0.15s; }
        .table tbody tr:nth-child(3) { animation-delay: 0.2s; }
        .table tbody tr:nth-child(4) { animation-delay: 0.25s; }
        .table tbody tr:nth-child(5) { animation-delay: 0.3s; }

        .stat-card:hover, .section-card:hover { transform: translateY(-6px) !important; box-shadow: 0 15px 35px rgba(139, 26, 26, 0.4) !important; transition: all 0.3s ease; }
        .table tbody tr:hover { background: var(--hover-subtle) !important; transform: translateX(6px); border-left: 3px solid var(--red-active); transition: all 0.25s ease; }
        .btn-action:hover, .btn-create:hover { transform: scale(1.05); box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .empty-state, td[colspan] { animation: pulse 3s infinite; } 
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header"><a href="#" class="sidebar-brand"><div class="brand-icon"><i class="fas fa-file-invoice"></i></div><div class="brand-text"><h4>DocGest · Univalle</h4><p>Sistema de Gestión Académica</p></div></a></div>
        <div class="univalle-section"><i class="fas fa-university"></i><span>UNIVALLE<br><small>Universidad del Valle</small></span></div>
        <nav class="sidebar-menu">
            <div class="nav-item"><a href="{{ route('director.dashboard') }}" class="nav-link"><i class="fas fa-home"></i><span>Inicio</span></a></div>
            <div class="nav-item"><a href="{{ route('director.paralelos') }}" class="nav-link {{ request()->routeIs('director.paralelos*') ? 'active' : '' }}"><i class="fas fa-layer-group"></i><span>Paralelos</span></a></div>
            <div class="nav-item"><a href="{{ route('director.docentes') }}" class="nav-link {{ request()->routeIs('director.docentes*') ? 'active' : '' }}"><i class="fas fa-chalkboard-teacher"></i><span>Docentes</span></a></div>
            <div class="nav-item"><a href="{{ route('director.estudiantes') }}" class="nav-link {{ request()->routeIs('director.estudiantes*') ? 'active' : '' }}"><i class="fas fa-user-graduate"></i><span>Estudiantes</span></a></div>
            <div class="nav-item"><a href="#" class="nav-link"><i class="fas fa-folder-open"></i><span>Documentos</span></a></div>
        </nav>
        <div class="sidebar-footer"><a href="{{ route('logout') }}" class="btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span></a><form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form></div>
    </aside>

    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="hamburger-btn" onclick="document.getElementById('sidebar').classList.toggle('show')"><i class="fas fa-bars"></i></button>
            <div class="navbar-title">Gestión de Paralelos</div>
        </div>
        <div class="navbar-right">
            <div class="director-badge"><i class="fas fa-gavel"></i>Director<span class="badge-active">Activo</span></div>
            <div class="notification-bell"><i class="fas fa-bell"></i><span class="notification-badge">3</span></div>
            <div class="user-profile"><div class="user-info"><div class="user-name">{{ Auth::user()->nombres ?? 'Director' }}</div><div class="user-role">Director de Carrera</div></div><div class="user-avatar">{{ substr(Auth::user()->nombres ?? 'D', 0, 1) }}</div></div>
        </div>
    </nav>

    <main class="main-content"><div class="content-wrapper">
        
        @if(session('success'))<div class="alert alert-dismissible fade show mb-4" style="background: rgba(46,204,113,0.1); border: 1px solid rgba(46,204,113,0.3); color: var(--status-approved); border-radius: 8px;"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button></div>@endif

        <div class="page-header">
            <div><h2>Paralelos Registrados</h2><p>Gestiona los paralelos académicos del sistema</p></div>
            <a href="{{ route('director.paralelos.crear') }}" class="btn-create"><i class="fas fa-plus"></i>Nuevo Paralelo</a>
        </div>

        <!-- Tabla de Paralelos - Diseño semi-transparente rojo vino -->
        <div style="background: linear-gradient(135deg, rgba(107, 26, 26, 0.4) 0%, rgba(74, 16, 16, 0.6) 100%); backdrop-filter: blur(10px); border: 1px solid rgba(122, 30, 30, 0.3); border-radius: 16px; overflow: hidden; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
            <div style="padding: 20px 24px; border-bottom: 1px solid rgba(122, 30, 30, 0.3); display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-layer-group" style="color: #B3261E; font-size: 1.1rem;"></i>
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0; color: #FFFFFF;">Lista de Paralelos</h3>
            </div>
            <div class="table-responsive" style="padding: 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: rgba(122, 30, 30, 0.2);">
                        <tr>
                            <th style="padding: 14px 20px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.75rem; color: #8A8A8A; border-bottom: 1px solid rgba(122, 30, 30, 0.2); text-align: left;">PARALELO</th>
                            <th style="padding: 14px 20px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.75rem; color: #8A8A8A; border-bottom: 1px solid rgba(122, 30, 30, 0.2); text-align: left;">MATERIA</th>
                            <th style="padding: 14px 20px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.75rem; color: #8A8A8A; border-bottom: 1px solid rgba(122, 30, 30, 0.2); text-align: left;">GESTIÓN</th>
                            <th style="padding: 14px 20px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.75rem; color: #8A8A8A; border-bottom: 1px solid rgba(122, 30, 30, 0.2); text-align: left;">DOCENTE</th>
                            <th style="padding: 14px 20px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.75rem; color: #8A8A8A; border-bottom: 1px solid rgba(122, 30, 30, 0.2); text-align: left;">ESTUDIANTES</th>
                            <th style="padding: 14px 20px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.75rem; color: #8A8A8A; border-bottom: 1px solid rgba(122, 30, 30, 0.2); text-align: left;">ESTADO</th>
                            <th style="padding: 14px 20px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.75rem; color: #8A8A8A; border-bottom: 1px solid rgba(122, 30, 30, 0.2); text-align: left;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paralelos ?? [] as $p)
                            <tr style="border-bottom: 1px solid rgba(122, 30, 30, 0.15); transition: background 0.2s ease;">
                                <td style="padding: 16px 20px;">
                                    <div style="width: 42px; height: 42px; background: linear-gradient(135deg, #7A1E1E 0%, #4A1010 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #FFFFFF; font-size: 1.1rem; box-shadow: 0 2px 8px rgba(122, 30, 30, 0.4);">
                                        {{ $p->letra }}
                                    </div>
                                </td>
                                <td style="padding: 16px 20px; font-weight: 600; color: #FFFFFF; font-size: 0.95rem;">
                                    {{ $p->materia?->nombre ?? 'Sin materia' }}
                                </td>
                                <td style="padding: 16px 20px; color: #C7C7C7; font-size: 0.9rem;">
                                    {{ $p->gestion?->nombre ?? 'N/A' }}
                                </td>
                                <td style="padding: 16px 20px;">
                                    @if($p->docenteCargo)
                                        <span style="color: #C7C7C7; font-size: 0.9rem;">{{ $p->docenteCargo->nombres }} {{ $p->docenteCargo->apellidos }}</span>
                                    @else
                                        <span style="color: #F2C94C; font-size: 0.85rem;"><i class="fas fa-exclamation-triangle me-1"></i>Sin asignar</span>
                                    @endif
                                </td>
                                <td style="padding: 16px 20px; text-align: center;">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; background: rgba(86, 204, 242, 0.12); color: #56CCF2; padding: 5px 14px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; border: 1px solid rgba(86, 204, 242, 0.2);">
                                        {{ $p->inscripciones?->count() ?? 0 }}
                                    </span>
                                </td>
                                <td style="padding: 16px 20px;">
                                    <span style="display: inline-block; padding: 5px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; {{ $p->estado === 'activo' ? 'background: rgba(46, 204, 113, 0.12); color: #2ECC71; border: 1px solid rgba(46, 204, 113, 0.25);' : 'background: rgba(239, 68, 68, 0.12); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.25);' }}">
                                        {{ ucfirst($p->estado) }}
                                    </span>
                                </td>
                                <td style="padding: 16px 20px;">
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('director.paralelos.detalle', $p->id) }}" title="Ver detalle" style="width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #C7C7C7; text-decoration: none; transition: all 0.2s ease;" onmouseover="this.style.background='rgba(122,30,30,0.3)';this.style.color='#FFFFFF';this.style.borderColor='rgba(122,30,30,0.5)';" onmouseout="this.style.background='rgba(255,255,255,0.05)';this.style.color='#C7C7C7';this.style.borderColor='rgba(255,255,255,0.1)';">
                                            <i class="fas fa-eye" style="font-size: 0.85rem;"></i>
                                        </a>
                                        <a href="{{ route('director.paralelos.editar', $p->id) }}" title="Editar" style="width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; background: rgba(242, 201, 76, 0.08); border: 1px solid rgba(242, 201, 76, 0.2); border-radius: 8px; color: #F2C94C; text-decoration: none; transition: all 0.2s ease;" onmouseover="this.style.background='rgba(242,201,76,0.2)';this.style.color='#FFFFFF';" onmouseout="this.style.background='rgba(242,201,76,0.08)';this.style.color='#F2C94C';">
                                            <i class="fas fa-edit" style="font-size: 0.85rem;"></i>
                                        </a>
                                        <a href="{{ route('director.paralelos.estudiantes', $p->id) }}" title="Ver estudiantes" style="width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; background: rgba(86, 204, 242, 0.08); border: 1px solid rgba(86, 204, 242, 0.2); border-radius: 8px; color: #56CCF2; text-decoration: none; transition: all 0.2s ease;" onmouseover="this.style.background='rgba(86,204,242,0.2)';this.style.color='#FFFFFF';" onmouseout="this.style.background='rgba(86,204,242,0.08)';this.style.color='#56CCF2';">
                                            <i class="fas fa-users" style="font-size: 0.85rem;"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 50px 20px; text-align: center;">
                                    <i class="fas fa-folder-open" style="font-size: 2.5rem; color: rgba(255,255,255,0.15); margin-bottom: 12px; display: block;"></i>
                                    <p style="color: #8A8A8A; margin: 0; font-size: 0.95rem;">No hay paralelos registrados</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div></main>

    <footer class="footer"><div class="footer-brand"><div class="footer-logo"><i class="fas fa-university"></i></div><div class="footer-text">DocGest · <strong>Universidad del Valle</strong><br><small>Sistema de Gestión Académica</small></div></div><div class="social-links"><a href="#" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a><a href="#" class="social-icon instagram"><i class="fab fa-instagram"></i></a><a href="#" class="social-icon whatsapp"><i class="fab fa-whatsapp"></i></a></div></footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>