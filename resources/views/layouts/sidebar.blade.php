<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    {{-- Anti-parpadeo: aplicar modo oscuro de inmediato antes de pintar --}}
    <script>
        (function() {
            try {
                if (localStorage.getItem('darkMode') === '1') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>
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

            /* Variables de color (Modo claro por defecto) */
            --bg-page:        #F5F3FF;
            --bg-card:        #ffffff;
            --bg-card-header: #ffffff;
            --bg-topbar:      #ffffff;
            --border-card:    #EDE9FE;
            --border-subtle:  #F5F3FF;
            --text-heading:   #1E1B4B;
            --text-body:      #374151;
            --text-muted:     #6B7280;
            --text-subtle:    #9CA3AF;
            --bg-input:       #FAFAFA;
            --bg-input-focus: #ffffff;
            --border-input:   #E5E7EB;
            --text-input:     #1E1B4B;
            --table-hover:    #FAFAFF;
            --table-th-bg:    #ffffff;
            --modal-bg:       #ffffff;
            --modal-border:   #EDE9FE;
        }

        html.dark {
            /* Variables de color (Modo oscuro - Morado de marca) */
            --bg-page:        #0F0A1C;
            --bg-card:        #181129;
            --bg-card-header: #150E24;
            --bg-topbar:      #150E24;
            --border-card:    #2F1E52;
            --border-subtle:  #241640;
            --text-heading:   #F5F3FF;
            --text-body:      #E4DEF4;
            --text-muted:     #A89BC4;
            --text-subtle:    #7D6F99;
            --bg-input:       #1F1436;
            --bg-input-focus: #281947;
            --border-input:   #3F286B;
            --text-input:     #F5F3FF;
            --table-hover:    #231540;
            --table-th-bg:    #150E24;
            --modal-bg:       #181129;
            --modal-border:   #341F5A;
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

        /* ══════════════════════════════════════════
           MODO OSCURO (html.dark)
        ══════════════════════════════════════════ */
        html.dark body {
            background: var(--bg-page);
            color: var(--text-body);
        }

        /* ── Topbar ── */
        html.dark .topbar {
            background: var(--bg-topbar) !important;
            border-bottom-color: var(--border-card) !important;
            box-shadow: 0 1px 10px rgba(0,0,0,.45) !important;
        }
        html.dark .topbar-title {
            color: var(--text-heading) !important;
        }
        html.dark .topbar-badge {
            background: #1F1436 !important;
            border-color: #341F5A !important;
            color: #C4B5FD !important;
        }
        html.dark .topbar-badge:hover {
            background: #2D1A52 !important;
            color: #FFFFFF !important;
        }
        html.dark #btnDarkMode {
            color: #FBBF24 !important;
        }
        html.dark #btnDarkMode:hover {
            background: #2D1A52 !important;
            color: #FDE68A !important;
        }
        html.dark .topbar-user-avatar {
            background: #3B1D7A !important;
            color: #DDD6FE !important;
            border: 1px solid #4C2882 !important;
        }
        html.dark .btn-topbar-logout {
            background: rgba(239,68,68,.2) !important;
            color: #FCA5A5 !important;
        }
        html.dark .btn-topbar-logout:hover {
            background: rgba(239,68,68,.35) !important;
            color: #FFFFFF !important;
        }

        /* ── Tarjetas, Paneles y Métricas ── */
        html.dark .card,
        html.dark .stat-card,
        html.dark .metric-card,
        html.dark .filtros-bar {
            background: var(--bg-card) !important;
            border-color: var(--border-card) !important;
            box-shadow: 0 4px 18px rgba(0,0,0,.35) !important;
        }
        html.dark .card-header {
            background: var(--bg-card-header) !important;
            border-bottom-color: var(--border-subtle) !important;
        }
        html.dark .card-title,
        html.dark .stat-value,
        html.dark .metric-val {
            color: var(--text-heading) !important;
        }
        html.dark .stat-label,
        html.dark .metric-lbl {
            color: var(--text-muted) !important;
        }
        html.dark .stat-icon.purple,
        html.dark .metric-icon.purple {
            background: #2C1A50 !important;
            color: #DDD6FE !important;
        }
        html.dark .stat-icon.red {
            background: rgba(239,68,68,.2) !important;
            color: #FCA5A5 !important;
        }
        html.dark .metric-icon.green {
            background: rgba(16,185,129,.2) !important;
            color: #6EE7B7 !important;
        }

        /* ── Tablas ── */
        html.dark table {
            color: var(--text-body);
        }
        html.dark thead th {
            background: var(--table-th-bg) !important;
            color: #DDD6FE !important;
            border-bottom: 2px solid var(--border-card) !important;
        }
        html.dark tbody td {
            color: var(--text-body) !important;
            border-bottom: 1px solid #231540 !important;
        }
        html.dark tbody tr:hover td {
            background: var(--table-hover) !important;
        }
        html.dark tfoot td,
        html.dark tfoot th {
            background: var(--table-th-bg) !important;
            color: var(--text-heading) !important;
            border-top: 2px solid var(--border-card) !important;
            border-bottom: 2px solid var(--border-card) !important;
        }
        html.dark .own-row td {
            background: #1E1238 !important;
        }
        html.dark .empty-state,
        html.dark .empty-row td {
            color: var(--text-subtle) !important;
        }
        html.dark .empty-state i,
        html.dark .empty-row i {
            color: #6D43B8 !important;
        }

        /* ── Inputs, Selects y Formularios ── */
        html.dark .form-input,
        html.dark .form-select,
        html.dark .filter-input,
        html.dark .filtro-input,
        html.dark .filtro-select,
        html.dark .qty-input,
        html.dark input[type="text"],
        html.dark input[type="number"],
        html.dark input[type="date"],
        html.dark input[type="password"],
        html.dark input[type="email"],
        html.dark input[type="search"],
        html.dark textarea,
        html.dark select {
            background: var(--bg-input) !important;
            color: var(--text-input) !important;
            border-color: var(--border-input) !important;
            color-scheme: dark;
        }
        html.dark input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); }
        html.dark .form-input:focus,
        html.dark .form-select:focus,
        html.dark .filter-input:focus,
        html.dark .filtro-input:focus,
        html.dark .filtro-select:focus,
        html.dark .qty-input:focus,
        html.dark input:focus,
        html.dark select:focus,
        html.dark textarea:focus {
            background: var(--bg-input-focus) !important;
            border-color: #8B5CF6 !important;
            box-shadow: 0 0 0 3px rgba(139,92,246,.25) !important;
            outline: none;
        }
        html.dark select option {
            background: #181129 !important;
            color: #F5F3FF !important;
        }
        html.dark ::placeholder {
            color: var(--text-subtle) !important;
            opacity: 1;
        }
        html.dark .form-label,
        html.dark .filtro-label {
            color: #D1D5DB !important;
        }
        html.dark .form-section-title {
            color: #C4B5FD !important;
            border-bottom-color: var(--border-card) !important;
        }
        html.dark .form-hint {
            color: var(--text-muted) !important;
        }

        /* ── Filtros, Tabs y Botones ── */
        html.dark .btn-filtrar {
            background: #251644 !important;
            color: #DDD6FE !important;
            border: 1.5px solid #442B70 !important;
        }
        html.dark .btn-filtrar:hover {
            background: #361F69 !important;
            color: #FFFFFF !important;
            border-color: #5B21B6 !important;
        }
        html.dark .filtros-bar .btn-filtrar {
            background: #7C3AED !important;
            border-color: #7C3AED !important;
            color: #FFFFFF !important;
        }
        html.dark .filtros-bar .btn-filtrar:hover {
            background: #6D28D9 !important;
            border-color: #6D28D9 !important;
        }
        html.dark .filter-btn:not(.active),
        html.dark .tab:not(.active) {
            background: #231540 !important;
            color: #DDD6FE !important;
            border-color: #3B2464 !important;
        }
        html.dark .filter-btn:not(.active):hover,
        html.dark .tab:not(.active):hover {
            background: #311E58 !important;
            color: #FFFFFF !important;
        }
        html.dark .btn-ver,
        html.dark .btn-editar {
            background: #28174E !important;
            color: #DDD6FE !important;
        }
        html.dark .btn-ver:hover,
        html.dark .btn-editar:hover {
            background: #3B2270 !important;
            color: #FFFFFF !important;
        }
        html.dark .btn-pass {
            background: rgba(245,158,11,.2) !important;
            color: #FDE68A !important;
        }
        html.dark .btn-pass:hover {
            background: rgba(245,158,11,.32) !important;
        }
        html.dark .btn-anular,
        html.dark .btn-eliminar {
            background: rgba(239,68,68,.2) !important;
            color: #FCA5A5 !important;
        }
        html.dark .btn-anular:hover,
        html.dark .btn-eliminar:hover {
            background: rgba(239,68,68,.32) !important;
        }
        html.dark .btn-toggle-on {
            background: rgba(16,185,129,.2) !important;
            color: #6EE7B7 !important;
        }
        html.dark .btn-toggle-on:hover {
            background: rgba(16,185,129,.32) !important;
        }
        html.dark .btn-toggle-off {
            background: rgba(156,163,175,.2) !important;
            color: #D1D5DB !important;
        }
        html.dark .btn-toggle-off:hover {
            background: rgba(156,163,175,.32) !important;
        }

        /* ── Modales ── */
        html.dark .modal-box,
        html.dark #modalLogout > div:last-child,
        html.dark #modalProximamente > div:last-child {
            background: var(--modal-bg) !important;
            border: 1px solid var(--modal-border) !important;
            box-shadow: 0 20px 60px rgba(0,0,0,.6) !important;
            color: var(--text-body) !important;
        }
        html.dark .modal-backdrop {
            background: rgba(10,5,20,.75) !important;
        }
        html.dark .modal-title,
        html.dark #modalLogout h3,
        html.dark #modalProximamente h3 {
            color: var(--text-heading) !important;
        }
        html.dark #modalLogout p,
        html.dark #modalProximamente p {
            color: var(--text-muted) !important;
        }
        html.dark .modal-close {
            color: var(--text-muted) !important;
        }
        html.dark .modal-close:hover {
            color: #EDE9FE !important;
        }
        html.dark .btn-cancelar,
        html.dark #modalLogout button:not([type="submit"]) {
            background: #251644 !important;
            color: #DDD6FE !important;
            border: 1.5px solid #442B70 !important;
        }
        html.dark .btn-cancelar:hover,
        html.dark #modalLogout button:not([type="submit"]):hover {
            background: #361F69 !important;
            color: #FFFFFF !important;
        }

        /* ── Badges de estado ── */
        html.dark .badge-completada,
        html.dark .badge-activo,
        html.dark .badge-entrada,
        html.dark .badge-empleado {
            background: rgba(16,185,129,.2) !important;
            color: #6EE7B7 !important;
            border: 1px solid rgba(16,185,129,.35) !important;
        }
        html.dark .badge-anulada,
        html.dark .badge-bajo,
        html.dark .badge-salida,
        html.dark .badge-red {
            background: rgba(239,68,68,.2) !important;
            color: #FCA5A5 !important;
            border: 1px solid rgba(239,68,68,.35) !important;
        }
        html.dark .badge-pendiente {
            background: rgba(245,158,11,.2) !important;
            color: #FDE68A !important;
            border: 1px solid rgba(245,158,11,.35) !important;
        }
        html.dark .badge-admin,
        html.dark .badge-purple,
        html.dark .badge-ok {
            background: rgba(124,58,237,.25) !important;
            color: #DDD6FE !important;
            border: 1px solid rgba(124,58,237,.4) !important;
        }
        html.dark .badge-inactivo {
            background: rgba(156,163,175,.2) !important;
            color: #D1D5DB !important;
            border: 1px solid rgba(156,163,175,.35) !important;
        }
        html.dark .badge-count {
            background: #2C1A50 !important;
            color: #DDD6FE !important;
        }
        html.dark .badge-count.zero {
            background: #1F1436 !important;
            color: #7D6F99 !important;
        }

        /* ── Mensajes Flash y Banners ── */
        html.dark .alert-success {
            background: rgba(16,185,129,.16) !important;
            color: #6EE7B7 !important;
            border: 1px solid rgba(16,185,129,.4) !important;
        }
        html.dark .alert-error {
            background: rgba(239,68,68,.16) !important;
            color: #FCA5A5 !important;
            border: 1px solid rgba(239,68,68,.4) !important;
        }
        html.dark .alert-warning {
            background: rgba(245,158,11,.16) !important;
            color: #FDE68A !important;
            border: 1px solid rgba(245,158,11,.4) !important;
        }
        html.dark .banner-bajo {
            background: rgba(239,68,68,.16) !important;
            color: #FCA5A5 !important;
            border: 1px solid rgba(239,68,68,.4) !important;
        }

        /* ── Componentes específicos ── */
        html.dark .stock-bar-bg {
            background: #251640 !important;
        }
        html.dark .stock-nums {
            color: var(--text-muted) !important;
        }
        html.dark .img-thumb {
            border-color: var(--border-card) !important;
        }
        html.dark .img-placeholder {
            background: #1F1436 !important;
            border-color: var(--border-card) !important;
            color: #8B5CF6 !important;
        }
        html.dark .carrito-vacio {
            color: var(--text-subtle) !important;
        }
        html.dark .carrito-vacio i {
            color: #7C3AED !important;
        }
        html.dark .carrito-item {
            border-bottom: 1px solid #231540 !important;
        }
        html.dark .carrito-nombre {
            color: var(--text-heading) !important;
        }
        html.dark .carrito-precio {
            color: var(--text-muted) !important;
        }
        html.dark .carrito-total-row {
            border-top: 2px solid var(--border-card) !important;
        }
        html.dark .carrito-total-num {
            color: #A78BFA !important;
        }
        html.dark .btn-quitar:hover {
            background: rgba(239,68,68,.25) !important;
        }

        /* ── Compatibilidad con estilos inline de texto ── */
        html.dark [style*="color:#1E1B4B"],
        html.dark [style*="color: #1E1B4B"],
        html.dark [style*="color:#1e1b4b"] {
            color: var(--text-heading) !important;
        }
        html.dark [style*="color:#374151"],
        html.dark [style*="color: #374151"] {
            color: var(--text-body) !important;
        }
        html.dark [style*="color:#6B7280"],
        html.dark [style*="color: #6B7280"] {
            color: var(--text-muted) !important;
        }

        /* ── Impresión siempre en blanco ── */
        @media print {
            html.dark, html.dark body {
                background: #ffffff !important;
                color: #000000 !important;
            }
        }

        /* ── Botón hamburguesa y backdrop móvil ── */
        .btn-hamburger {
            display: none;
            align-items: center; justify-content: center;
            width: 38px; height: 38px; border-radius: 9px;
            background: #F5F3FF; border: 1.5px solid #DDD6FE;
            color: #4A1D96; font-size: 1.15rem; cursor: pointer;
            transition: all .2s; margin-right: 6px;
        }
        .btn-hamburger:hover { background: #EDE9FE; }

        .sidebar-backdrop {
            display: none;
            position: fixed; inset: 0;
            background: rgba(15, 10, 28, 0.65);
            backdrop-filter: blur(2px);
            z-index: 45;
            transition: opacity .3s ease;
        }
        .sidebar-backdrop.active {
            display: block;
        }

        html.dark .btn-hamburger {
            background: #1F1436 !important;
            border-color: #341F5A !important;
            color: #DDD6FE !important;
        }
        html.dark .btn-hamburger:hover {
            background: #2D1A52 !important;
            color: #FFFFFF !important;
        }
        html.dark .sidebar-backdrop {
            background: rgba(10, 5, 22, 0.8) !important;
        }

        /* ── Responsive Móvil (< 768px) ── */
        @media (max-width: 768px) {
            .btn-hamburger { display: inline-flex !important; }

            .sidebar {
                transform: translateX(-100%);
                box-shadow: 0 0 35px rgba(0, 0, 0, .45);
            }
            .sidebar.open {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0 !important;
            }

            .topbar {
                padding: 0 16px !important;
            }
            .topbar-title {
                font-size: 1.05rem !important;
            }

            .page-content {
                padding: 16px !important;
            }

            /* Tablas con overflow-x: auto */
            .table-wrap,
            table {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
            .card {
                overflow-x: auto;
            }

            /* Filtros y tarjetas apilados en una columna */
            .stats-row,
            .grid-2,
            .config-grid,
            .inv-grid,
            .metrics-group,
            .metrics-header,
            .filters,
            .filtros-bar,
            .toolbar {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                width: 100% !important;
                gap: 12px !important;
            }

            .stat-card,
            .metric-card {
                width: 100% !important;
                min-width: 0 !important;
                box-sizing: border-box !important;
            }

            .filter-input,
            .filtro-input,
            .filtro-select,
            .filter-btn,
            .btn-filtrar,
            .btn-limpiar,
            .btn-nuevo {
                width: 100% !important;
                min-width: 0 !important;
                box-sizing: border-box !important;
                justify-content: center !important;
            }
        }
    </style>
</head>
<body>

{{-- ═══════════════════════════════════════
     SIDEBAR
════════════════════════════════════════ --}}
{{-- Backdrop para celular (< 768px) --}}
<div id="sidebarBackdrop" class="sidebar-backdrop" onclick="toggleSidebar(false)"></div>

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
        <div style="display:flex; align-items:center; gap:10px;">
            <button type="button" class="btn-hamburger" id="btnToggleSidebar"
                onclick="toggleSidebar()" aria-label="Abrir o cerrar menú" title="Menú">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
        </div>
        <div class="topbar-right">
            {{-- Modo oscuro/claro --}}
            <button type="button" class="topbar-badge" id="btnDarkMode" title="Modo oscuro"
                onclick="toggleDarkMode()" aria-label="Cambiar modo oscuro o claro">
                <i class="fas fa-moon" id="darkIcon"></i>
            </button>
            <script>
                try {
                    if (document.documentElement.classList.contains('dark')) {
                        var _b = document.getElementById('btnDarkMode');
                        var _i = document.getElementById('darkIcon');
                        if (_b) _b.setAttribute('title', 'Modo claro');
                        if (_i) _i.className = 'fas fa-sun';
                    }
                } catch(e) {}
            </script>

            {{-- Campana de alertas --}}
            <div class="topbar-badge">
                <i class="fas fa-bell text-sm"></i>
                @if($alertasStock > 0)
                    <span class="badge-dot">{{ $alertasStock > 9 ? '9+' : $alertasStock }}</span>
                @endif
            </div>
            {{-- Avatar --}}
            <div class="topbar-badge topbar-user-avatar" style="background:#EDE9FE; color:#4A1D96; font-weight:700; font-size:.85rem;">
                {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}
            </div>
            {{-- [4] Cerrar sesión: tooltip + modal de confirmación --}}
            <button type="button" class="topbar-badge btn-topbar-logout" title="Cerrar sesión"
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
function syncDarkModeUI(isDark) {
    try {
        var btn = document.getElementById('btnDarkMode');
        var icon = document.getElementById('darkIcon');
        if (btn) {
            btn.setAttribute('title', isDark ? 'Modo claro' : 'Modo oscuro');
        }
        if (icon) {
            icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        }
    } catch (e) {}
}

function toggleDarkMode() {
    try {
        var isDark = document.documentElement.classList.toggle('dark');
        try {
            localStorage.setItem('darkMode', isDark ? '1' : '0');
        } catch (e) {}
        syncDarkModeUI(isDark);
    } catch (err) {}
}

document.addEventListener('DOMContentLoaded', function() {
    try {
        var isDark = document.documentElement.classList.contains('dark');
        syncDarkModeUI(isDark);
    } catch (e) {}
});

function proximamente() {
    document.getElementById('modalProximamente').style.display = 'flex';
}

function toggleSidebar(force) {
    var sidebar = document.querySelector('.sidebar');
    var backdrop = document.getElementById('sidebarBackdrop');
    if (!sidebar) return;
    var shouldOpen = (typeof force === 'boolean') ? force : !sidebar.classList.contains('open');
    if (shouldOpen) {
        sidebar.classList.add('open');
        if (backdrop) backdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
    } else {
        sidebar.classList.remove('open');
        if (backdrop) backdrop.classList.remove('active');
        document.body.style.overflow = '';
    }
}

window.addEventListener('resize', function() {
    if (window.innerWidth >= 768) {
        toggleSidebar(false);
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        toggleSidebar(false);
        document.getElementById('modalProximamente').style.display = 'none';
        document.getElementById('modalLogout').style.display = 'none';
    }
});
</script>

</body>
</html>
