<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') — DisneyStock</title>
    <link rel="icon" type="image/png" href="{{ asset('img/LogoSoloo.png') }}">

    {{-- Fuentes --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Tailwind 4 via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-w: 260px;
            --header-h:  64px;
            --primary:   #4A1D96;
            --primary-l: #7C3AED;
            --accent:    #F59E0B;
            --sidebar:   #1E0747;
        }
        body { font-family: 'Inter', sans-serif; background: #F5F3FF; }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--sidebar);
            display: flex; flex-direction: column;
            z-index: 50;
            transition: transform .3s ease;
        }
        .sidebar-brand {
            display: flex; align-items: center; gap: 10px;
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-brand img { height: 40px; width: auto; object-fit: contain; }
        .sidebar-brand span {
            font-family: 'Outfit', sans-serif;
            font-size: 1.35rem; font-weight: 800;
            color: #fff; letter-spacing: -.5px;
        }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .sidebar-section {
            font-size: .68rem; font-weight: 700; letter-spacing: .1em;
            color: rgba(255,255,255,.35);
            padding: 12px 8px 6px; text-transform: uppercase;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            color: rgba(255,255,255,.7);
            text-decoration: none; font-size: .9rem; font-weight: 500;
            transition: all .2s ease; margin-bottom: 2px;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255,255,255,.1);
            color: #fff;
        }
        .sidebar-link.active { background: var(--primary-l); color: #fff; }
        .sidebar-link i { width: 18px; text-align: center; font-size: .95rem; }
        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-user {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            background: rgba(255,255,255,.06); margin-bottom: 8px;
        }
        .sidebar-user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--primary-l);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #fff; font-size: .9rem; flex-shrink: 0;
        }
        .sidebar-user-name { font-size: .85rem; font-weight: 600; color: #fff; }
        .sidebar-user-role {
            font-size: .72rem; color: rgba(255,255,255,.5);
            text-transform: capitalize;
        }
        .btn-logout {
            display: flex; align-items: center; gap: 8px;
            width: 100%; padding: 9px 12px; border-radius: 10px;
            background: rgba(239,68,68,.15); color: #FCA5A5;
            border: none; cursor: pointer; font-size: .87rem; font-weight: 500;
            transition: all .2s ease;
        }
        .btn-logout:hover { background: rgba(239,68,68,.3); color: #fff; }

        /* ── Main ── */
        .main-wrapper { margin-left: var(--sidebar-w); min-height: 100vh; display: flex; flex-direction: column; }
        .topbar {
            position: sticky; top: 0; z-index: 40;
            height: var(--header-h);
            background: #ffffff;
            border-bottom: 1px solid #EDE9FE;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px;
            box-shadow: 0 1px 6px rgba(74,29,150,.08);
        }
        .topbar-title { font-family: 'Outfit', sans-serif; font-size: 1.25rem; font-weight: 700; color: #1E1B4B; }
        .topbar-right { display: flex; align-items: center; gap: 14px; }
        .topbar-badge {
            position: relative; width: 38px; height: 38px; border-radius: 50%;
            background: #F5F3FF; border: 1px solid #EDE9FE;
            display: flex; align-items: center; justify-content: center;
            color: #7C3AED; cursor: pointer; transition: background .2s;
        }
        .topbar-badge:hover { background: #EDE9FE; }
        .badge-dot {
            position: absolute; top: 4px; right: 4px;
            min-width: 16px; height: 16px; border-radius: 8px;
            background: #EF4444; border: 2px solid #fff;
            font-size: .6rem; font-weight: 700; color: #fff;
            display: flex; align-items: center; justify-content: center;
            padding: 0 3px; line-height: 1;
        }
        .page-content { flex: 1; padding: 28px; }

        /* ── Cards ── */
        .card { background: #fff; border-radius: 14px; border: 1px solid #EDE9FE; box-shadow: 0 2px 12px rgba(74,29,150,.06); }
        .card-header { padding: 18px 22px; border-bottom: 1px solid #F5F3FF; display: flex; align-items: center; justify-content: space-between; }
        .card-title { font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 1rem; color: #1E1B4B; }
        .card-body { padding: 22px; }

        /* ── Flash messages ── */
        .alert-success { background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; border-radius: 10px; padding: 12px 18px; display: flex; align-items: center; gap: 10px; }
        .alert-error   { background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; border-radius: 10px; padding: 12px 18px; display: flex; align-items: center; gap: 10px; }
        .alert-warning { background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; border-radius: 10px; padding: 12px 18px; display: flex; align-items: center; gap: 10px; margin-top: 10px; }
    </style>
</head>
<body>

{{-- ═══════════════════════════════════════
     SIDEBAR
════════════════════════════════════════ --}}
<aside class="sidebar">

    {{-- Logo --}}
    <div class="sidebar-brand">
        <img src="{{ asset('img/BlancoSolo.png') }}" alt="DisneyStock">
        <span>DisneyStock</span>
    </div>

    {{-- Navegación --}}
    <nav class="sidebar-nav">
        <div class="sidebar-section">Principal</div>

        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>

        <div class="sidebar-section">Inventario</div>

        <a href="{{ route('productos.index') }}"
           class="sidebar-link {{ request()->routeIs('productos.*') ? 'active' : '' }}">
            <i class="fas fa-box"></i> Productos
        </a>
        <a href="{{ route('inventario.index') }}"
           class="sidebar-link {{ request()->routeIs('inventario.*') ? 'active' : '' }}">
            <i class="fas fa-warehouse"></i> Inventario
        </a>

        @if(Auth::user()->esAdmin())
        <a href="{{ route('categorias.index') }}"
           class="sidebar-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i> Categorías
        </a>
        @endif

        <div class="sidebar-section">Ventas</div>

        <a href="{{ route('ventas.index') }}"
           class="sidebar-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}">
            <i class="fas fa-cash-register"></i> Ventas
        </a>
        <a href="{{ route('reportes.index') }}"
           class="sidebar-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Reportes
        </a>

        @if(Auth::user()->esAdmin())
        <div class="sidebar-section">Administración</div>
        <a href="{{ route('usuarios.index') }}"
           class="sidebar-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
            <i class="fas fa-users-cog"></i> Usuarios
        </a>
        <a href="{{ route('configuracion.index') }}"
           class="sidebar-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
            <i class="fas fa-gear"></i> Configuración
        </a>
        @endif
    </nav>

    {{-- Footer usuario (solo info, sin logout) --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}
            </div>
            <div>
                <div class="sidebar-user-name">{{ Auth::user()->nombre }}</div>
                <div class="sidebar-user-role">{{ Auth::user()->rol->nombre ?? 'usuario' }}</div>
            </div>
        </div>
    </div>

</aside>

{{-- ═══════════════════════════════════════
     CONTENIDO PRINCIPAL
════════════════════════════════════════ --}}
<div class="main-wrapper">

    {{-- Topbar --}}
    <header class="topbar">
        <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
        <div class="topbar-right">
            {{-- Campana de alertas --}}
            <div class="topbar-badge">
                <i class="fas fa-bell text-sm"></i>
                @if($alertasStock > 0)
                    <span class="badge-dot">{{ $alertasStock > 9 ? '9+' : $alertasStock }}</span>
                @endif
            </div>
            {{-- Avatar --}}
            <div class="topbar-badge" style="background:#EDE9FE; color:#4A1D96; font-weight:700; font-size:.85rem;">
                {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}
            </div>
            {{-- [4] Cerrar sesión: tooltip + modal de confirmación --}}
            <button type="button" class="topbar-badge" title="Cerrar sesión"
                onclick="document.getElementById('modalLogout').style.display='flex'"
                style="background:#FEE2E2; color:#DC2626; border:none; cursor:pointer;">
                <i class="fas fa-right-from-bracket" style="font-size:.9rem;"></i>
            </button>
        </div>
    </header>

    {{-- Flash messages --}}
    <div style="padding: 0 28px; margin-top: 18px;">
        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('warning'))
            @php $warnings = is_array(session('warning')) ? session('warning') : [session('warning')]; @endphp
            @foreach($warnings as $w)
                <div class="alert-warning">
                    <i class="fas fa-triangle-exclamation"></i>
                    <span>{{ $w }}</span>
                </div>
            @endforeach
        @endif
        @if(session('error') && !request()->routeIs('usuarios.*'))
            <div class="alert-error">
                <i class="fas fa-circle-xmark"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
    </div>

    {{-- Contenido de cada vista --}}
    <main class="page-content">
        @yield('content')
    </main>

</div>

{{-- Modal: cerrar sesión --}}
<div id="modalLogout" style="display:none; position:fixed; inset:0; z-index:2000; align-items:center; justify-content:center; padding:24px;">
    <div style="position:absolute; inset:0; background:rgba(30,7,71,.45);" onclick="this.parentElement.style.display='none'"></div>
    <div style="position:relative; background:#fff; border-radius:16px; max-width:380px; width:100%; padding:32px 28px; box-shadow:0 16px 48px rgba(74,29,150,.18); text-align:center;">
        <div style="width:48px; height:48px; border-radius:14px; background:#FEE2E2; display:flex; align-items:center; justify-content:center; font-size:1.3rem; color:#DC2626; margin:0 auto 16px;">
            <i class="fas fa-right-from-bracket"></i>
        </div>
        <h3 style="font-family:'Outfit',sans-serif; font-size:1.1rem; font-weight:800; color:#1E1B4B; margin-bottom:8px;">¿Seguro que deseas cerrar sesión?</h3>
        <p style="font-size:.87rem; color:#6B7280; line-height:1.6; margin-bottom:24px;">Tu sesión se cerrará y deberás volver a iniciar sesión para acceder al sistema.</p>
        <div style="display:flex; gap:10px; justify-content:center;">
            <button onclick="document.getElementById('modalLogout').style.display='none'"
                style="padding:9px 22px; background:#F5F3FF; color:#5B21B6; border:1.5px solid #DDD6FE; border-radius:9px; font-size:.88rem; font-weight:600; cursor:pointer;"
                onmouseover="this.style.background='#EDE9FE'" onmouseout="this.style.background='#F5F3FF'">
                Cancelar
            </button>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit"
                    style="padding:9px 22px; background:#DC2626; color:#fff; border:2px solid #DC2626; border-radius:9px; font-family:'Outfit',sans-serif; font-size:.88rem; font-weight:700; cursor:pointer;"
                    onmouseover="this.style.background='#B91C1C';this.style.borderColor='#B91C1C'"
                    onmouseout="this.style.background='#DC2626';this.style.borderColor='#DC2626'">
                    <i class="fas fa-right-from-bracket"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Modal: próximamente --}}
<div id="modalProximamente" style="display:none; position:fixed; inset:0; z-index:2000; align-items:center; justify-content:center; padding:24px;">
    <div style="position:absolute; inset:0; background:rgba(30,7,71,.45);" onclick="this.parentElement.style.display='none'"></div>
    <div style="position:relative; background:#fff; border-radius:16px; max-width:360px; width:100%; padding:32px 28px; box-shadow:0 16px 48px rgba(74,29,150,.18); text-align:center;">
        <div style="width:48px; height:48px; border-radius:14px; background:#EDE9FE; display:flex; align-items:center; justify-content:center; font-size:1.3rem; color:#7C3AED; margin:0 auto 16px;">
            <i class="fas fa-clock"></i>
        </div>
        <h3 style="font-family:'Outfit',sans-serif; font-size:1.1rem; font-weight:800; color:#1E1B4B; margin-bottom:8px;">Módulo en desarrollo</h3>
        <p style="font-size:.88rem; color:#6B7280; line-height:1.65; margin-bottom:22px;">Este módulo estará disponible pronto.</p>
        <button onclick="document.getElementById('modalProximamente').style.display='none'"
            style="padding:10px 28px; background:#4A1D96; color:#fff; border:2px solid #4A1D96; border-radius:10px; font-family:'Outfit',sans-serif; font-size:.92rem; font-weight:700; cursor:pointer;"
            onmouseover="this.style.background='#7C3AED';this.style.borderColor='#7C3AED'"
            onmouseout="this.style.background='#4A1D96';this.style.borderColor='#4A1D96'">
            Entendido
        </button>
    </div>
</div>

<script>
function proximamente() {
    document.getElementById('modalProximamente').style.display = 'flex';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('modalProximamente').style.display = 'none';
        document.getElementById('modalLogout').style.display = 'none';
    }
});
</script>

</body>
</html>
