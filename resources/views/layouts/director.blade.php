<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel del Director - DocGest Univalle')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
    :root{
        --bg-primary:#120607;--bg-secondary:#1A0A0A;--bg-panel:#220B0B;--sidebar-base:#1E0A0A;--sidebar-deep:#4A1010;
        --red-primary:#6B1A1A;--red-active:#7A1E1E;--red-hover:#8B2A2A;--red-button:#B3261E;
        --text-primary:#FFFFFF;--text-secondary:#C7C7C7;--text-muted:#8A8A8A;
        --border-soft:rgba(255,255,255,0.08);--border-table:rgba(255,255,255,0.05);--hover-subtle:rgba(255,255,255,0.02);
        --status-pending:#F2C94C;--status-approved:#2ECC71;--status-info:#56CCF2;--status-purple:#9B59B6;
    }
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Inter',sans-serif;background:var(--bg-primary);color:var(--text-primary);min-height:100vh}
    
    /* SIDEBAR */
    .sidebar{position:fixed;top:0;left:0;width:280px;height:100vh;background:linear-gradient(180deg,var(--sidebar-base) 0%,var(--sidebar-deep) 100%);z-index:1040;display:flex;flex-direction:column;border-right:1px solid var(--border-soft)}
    .sidebar-header{padding:20px;border-bottom:1px solid var(--border-soft)}
    .sidebar-brand{display:flex;align-items:center;gap:12px;text-decoration:none;color:var(--text-primary)}
    .brand-icon{width:40px;height:40px;background:var(--red-active);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.2rem}
    .brand-text h4{font-size:1rem;font-weight:700;margin:0}
    .brand-text p{font-size:.7rem;margin:0;color:var(--text-secondary);opacity:.7}
    .univalle-section{padding:16px 20px;border-bottom:1px solid var(--border-soft);display:flex;align-items:center;gap:10px}
    .univalle-section i{font-size:1.5rem;color:var(--text-muted);opacity:.6}
    .univalle-section span{color:var(--text-secondary);font-size:.85rem}
    .sidebar-menu{flex:1;padding:20px 12px}
    .nav-item{margin-bottom:4px}
    .nav-link{display:flex;align-items:center;gap:12px;padding:12px 16px;color:var(--text-secondary);text-decoration:none;border-radius:8px;transition:all .2s ease;font-size:.9rem;font-weight:500}
    .nav-link:hover,.nav-link.active{background:var(--red-active);color:var(--text-primary)}
    .nav-link i{width:20px;text-align:center;font-size:1rem}
    .sidebar-footer{padding:20px;border-top:1px solid var(--border-soft)}
    .btn-logout{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px;background:rgba(179,38,30,.15);color:var(--text-secondary);border:1px solid var(--red-primary);border-radius:8px;text-decoration:none;font-weight:500;transition:all .2s ease}
    .btn-logout:hover{background:var(--red-active);color:var(--text-primary)}
    
    /* TOP NAVBAR */
    .top-navbar{background:var(--bg-secondary);border-bottom:1px solid var(--border-soft);padding:0 24px;height:70px;position:fixed;top:0;left:280px;right:0;z-index:1030;display:flex;align-items:center;justify-content:space-between}
    .navbar-left{display:flex;align-items:center;gap:16px}
    .hamburger-btn{background:transparent;border:none;color:var(--text-secondary);font-size:1.3rem;padding:8px;cursor:pointer}
    .hamburger-btn:hover{color:var(--text-primary)}
    .navbar-title{font-size:1.1rem;font-weight:600;color:var(--text-primary)}
    .navbar-right{display:flex;align-items:center;gap:20px}
    .director-badge{display:flex;align-items:center;gap:8px;padding:6px 14px;background:rgba(107,26,26,.3);border:1px solid var(--red-primary);border-radius:6px;font-size:.8rem;color:var(--text-secondary)}
    .director-badge i{color:var(--red-active)}
    .director-badge .badge-active{background:var(--red-button);color:var(--text-primary);font-size:.65rem;padding:2px 8px;border-radius:4px}
    .notification-bell{position:relative;cursor:pointer;color:var(--text-muted);font-size:1.2rem}
    .notification-badge{position:absolute;top:-6px;right:-6px;background:var(--red-active);color:var(--text-primary);font-size:.6rem;padding:2px 5px;border-radius:10px;font-weight:600}
    .user-profile{display:flex;align-items:center;gap:12px}
    .user-avatar{width:38px;height:38px;background:var(--red-primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.9rem;color:var(--text-primary)}
    .user-info{text-align:right;line-height:1.2}
    .user-name{font-weight:600;font-size:.9rem;color:var(--text-primary)}
    .user-role{font-size:.7rem;color:var(--text-muted)}
    
    /* MAIN CONTENT */
    .main-content{margin-left:280px;padding-top:90px;padding-bottom:80px;min-height:100vh;background:var(--bg-primary)}
    .content-wrapper{padding:0 24px}
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
    .page-header h2{font-size:1.3rem;font-weight:600;margin:0}
    .page-header p{color:var(--text-muted);margin:4px 0 0;font-size:.9rem}
    
    /* STATS */
    .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px}
    .stat-card{background:var(--bg-secondary);border:1px solid var(--border-soft);border-radius:12px;padding:20px;border-left:4px solid;display:flex;align-items:center;justify-content:space-between;animation:fadeInUp .5s ease-out}
    .stat-card:nth-child(1){border-left-color:var(--red-active)}
    .stat-card:nth-child(2){border-left-color:var(--status-pending)}
    .stat-card:nth-child(3){border-left-color:var(--status-approved)}
    .stat-card:nth-child(4){border-left-color:var(--status-purple)}
    .stat-info .stat-label{color:var(--text-secondary);font-size:.85rem;margin-bottom:8px}
    .stat-info .stat-value{font-size:1.8rem;font-weight:700;margin:0}
    .stat-icon{width:45px;height:45px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.1rem}
    .stat-card:nth-child(1) .stat-icon{background:rgba(122,30,30,.3);color:var(--red-active)}
    .stat-card:nth-child(2) .stat-icon{background:rgba(242,201,76,.15);color:var(--status-pending)}
    .stat-card:nth-child(3) .stat-icon{background:rgba(46,204,113,.15);color:var(--status-approved)}
    .stat-card:nth-child(4) .stat-icon{background:rgba(155,89,182,.15);color:var(--status-purple)}
    
    /* CONTENT */
    .content-row{display:grid;grid-template-columns:2fr 1fr;gap:20px}
    .section-card{background:var(--bg-secondary);border:1px solid var(--border-soft);border-radius:12px;overflow:hidden}
    .section-header{padding:16px 20px;border-bottom:1px solid var(--border-soft);display:flex;align-items:center;gap:8px}
    .section-header h3{font-size:1rem;font-weight:600;margin:0}
    .section-header i{color:var(--red-active)}
    .section-body{padding:24px}
    
    /* TABLES */
    .table{color:var(--text-primary);margin-bottom:0}
    .table thead th{background:rgba(255,255,255,.03);border-bottom:1px solid var(--border-soft);color:var(--text-muted);font-size:.75rem;text-transform:uppercase;padding:12px 16px;font-weight:600}
    .table tbody td{border-bottom:1px solid var(--border-table);padding:14px 16px;vertical-align:middle}
    .table tbody tr:hover{background:var(--hover-subtle)}
    
    /* ACTIONS */
    .quick-actions .action-item{display:flex;align-items:center;gap:12px;padding:14px 16px;background:var(--bg-panel);border:1px solid var(--border-soft);border-radius:8px;margin-bottom:10px;text-decoration:none;color:var(--text-primary);transition:all .2s ease;font-size:.9rem;font-weight:500}
    .quick-actions .action-item:hover{background:var(--hover-subtle);border-color:var(--red-active);transform:translateX(4px)}
    
    /* FOOTER */
    .footer{position:fixed;bottom:0;left:280px;right:0;height:70px;background:var(--bg-secondary);border-top:1px solid var(--border-soft);z-index:1020;display:flex;align-items:center;justify-content:space-between;padding:0 24px}
    .footer-brand{display:flex;align-items:center;gap:12px}
    .footer-logo{width:40px;height:40px;border-radius:50%;border:2px solid var(--border-soft);display:flex;align-items:center;justify-content:center;background:var(--bg-panel)}
    .footer-logo i{color:var(--red-active)}
    .footer-text{color:var(--text-muted);font-size:.85rem}
    .footer-text strong{color:var(--red-active)}
    .social-links{display:flex;gap:12px}
    .social-icon{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);text-decoration:none;transition:all .2s ease}
    .social-icon:hover{transform:scale(1.1);color:var(--text-primary)}
    .social-icon.facebook{background:#3b5998}
    .social-icon.instagram{background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888)}
    .social-icon.whatsapp{background:#25d366}
    
    /* ANIMATIONS */
    @keyframes fadeInUp{from{opacity:0;transform:translateY(25px)}to{opacity:1;transform:translateY(0)}}
    @keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.02)}}
    .stat-card:hover{transform:translateY(-5px);box-shadow:0 10px 30px rgba(139,26,26,.3);transition:all .3s ease}
    .btn-action:hover{transform:scale(1.05);box-shadow:0 5px 15px rgba(0,0,0,.3)}
    
    /* RESPONSIVE */
    @media(max-width:992px){
        .stats-grid{grid-template-columns:repeat(2,1fr)}
        .content-row{grid-template-columns:1fr}
        .sidebar{transform:translateX(-100%)}
        .sidebar.show{transform:translateX(0)}
        .top-navbar,.main-content,.footer{left:0}
    }
    </style>
    @stack('styles')
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="#" class="sidebar-brand">
                <div class="brand-icon"><i class="fas fa-file-invoice"></i></div>
                <div class="brand-text">
                    <h4>DocGest · Univalle</h4>
                    <p>Sistema de Gestión Académica</p>
                </div>
            </a>
        </div>
        <div class="univalle-section">
            <i class="fas fa-university"></i>
            <span>UNIVALLE<br><small>Universidad del Valle</small></span>
        </div>
        <nav class="sidebar-menu">
            <div class="nav-item">
                <a href="{{ route('director.dashboard') }}" class="nav-link {{ request()->routeIs('director.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i><span>Inicio</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('director.paralelos') }}" class="nav-link {{ request()->routeIs('director.paralelos*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i><span>Paralelos</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('director.docentes') }}" class="nav-link {{ request()->routeIs('director.docentes*') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-teacher"></i><span>Docentes</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('director.estudiantes') }}" class="nav-link {{ request()->routeIs('director.estudiantes*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i><span>Estudiantes</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#" class="nav-link"><i class="fas fa-folder-open"></i><span>Documentos</span></a>
            </div>
        </nav>
        <div class="sidebar-footer">
            <a href="{{ route('logout') }}" class="btn-logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </aside>

    <!-- TOP NAVBAR -->
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="hamburger-btn" onclick="document.getElementById('sidebar').classList.toggle('show')"><i class="fas fa-bars"></i></button>
            <div class="navbar-title">@yield('page-title', 'Panel de Control')</div>
        </div>
        <div class="navbar-right">
            <div class="director-badge">
                <i class="fas fa-gavel"></i>Director<span class="badge-active">Activo</span>
            </div>
            <div class="notification-bell"><i class="fas fa-bell"></i><span class="notification-badge">3</span></div>
            <div class="user-profile">
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->nombres ?? 'Director' }}</div>
                    <div class="user-role">Director de Carrera</div>
                </div>
                <div class="user-avatar">{{ substr(Auth::user()->nombres ?? 'D', 0, 1) }}</div>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="content-wrapper">
            @if(session('success'))
            <div class="alert alert-dismissible fade show mb-4" style="background:rgba(46,204,113,.1);border:1px solid rgba(46,204,113,.3);color:var(--status-approved);border-radius:8px">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @yield('content')
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-brand">
            <div class="footer-logo"><i class="fas fa-university"></i></div>
            <div class="footer-text">
                DocGest · <strong>Universidad del Valle</strong><br>
                <small>Sistema de Gestión Académica</small>
            </div>
        </div>
        <div class="social-links">
            <a href="#" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-icon instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-icon whatsapp"><i class="fab fa-whatsapp"></i></a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>