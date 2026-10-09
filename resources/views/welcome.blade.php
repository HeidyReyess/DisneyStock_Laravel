<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DisneyStock — Sistema de Gestión para Variedades Disney</title>
    <meta name="description" content="Sistema web de gestión de inventario y ventas para Variedades Disney.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary:       #4A1D96;
            --primary-light: #7C3AED;
            --primary-ultra: #F5F3FF;
            --accent:        #F59E0B;
            --accent-dark:   #D97706;
            --sky:           #3B82F6;
            --text:          #1E1B4B;
            --text-muted:    #6B7280;
            --white:         #ffffff;
            --border:        #DDD6FE;
            --navbar-h:      72px;
        }
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; color: var(--text); background: var(--white); overflow-x: hidden; }
        .container { max-width: 1180px; margin: 0 auto; padding: 0 24px; }

        /* ── Navbar ── */
        .navbar {
            position: fixed; top:0; left:0; right:0; z-index:1000;
            height: var(--navbar-h);
            background: #ffffff;
            border-bottom: 1px solid rgba(221,214,254,.6);
            box-shadow: 0 1px 6px rgba(74,29,150,.08);
            transition: box-shadow .3s ease;
        }
        .navbar.scrolled { box-shadow: 0 2px 14px rgba(74,29,150,.13); }
        .navbar-inner { display:flex; align-items:center; justify-content:space-between; height:var(--navbar-h); font-size:0; }
        .navbar-inner > * { font-size:1rem; }
        .nav-links { display:flex; align-items:center; gap:8px; list-style:none; }
        .nav-link { padding:8px 14px; color:var(--text-muted); text-decoration:none; font-size:.92rem; font-weight:500; border-radius:8px; transition:all .2s ease; }
        .nav-link:hover { color:var(--primary); background:var(--primary-ultra); }
        .nav-btn-ingresar {
            padding:9px 22px; background:var(--primary); color:var(--white);
            text-decoration:none; font-size:.9rem; font-weight:700; border-radius:10px;
            margin-left:8px; transition:all .2s ease;
            box-shadow:0 4px 14px rgba(74,29,150,.3);
        }
        .nav-btn-ingresar:hover { background:var(--primary-light); transform:translateY(-1px); box-shadow:0 6px 20px rgba(74,29,150,.4); }

        /* ── Carrusel ── */
        #inicio { position:relative; height:100vh; overflow:hidden; margin-top:0; }
        .slide { position:absolute; inset:0; opacity:0; transition:opacity .8s ease; }
        .slide.active { opacity:1; z-index:1; }
        .slide-bg { width:100%; height:100%; object-fit:cover; }
        .slide::after { content:''; position:absolute; inset:0; background:rgba(44,10,100,.70); }
        .slide-content {
            position:absolute; inset:0; z-index:2;
            display:flex; flex-direction:column; align-items:center; justify-content:center;
            text-align:center; padding:0 24px; padding-top:var(--navbar-h);
        }
        .slide-content h1 { font-family:'Outfit',sans-serif; font-size:clamp(2rem,5vw,3.5rem); font-weight:900; color:#fff; margin-bottom:16px; line-height:1.15; }
        .slide-content p { font-size:clamp(.95rem,2vw,1.15rem); color:rgba(255,255,255,.85); max-width:620px; margin-bottom:32px; line-height:1.7; }
        .hero-btns { display:flex; gap:14px; flex-wrap:wrap; justify-content:center; }
        .btn { padding:13px 28px; border-radius:12px; font-weight:700; font-size:.95rem; text-decoration:none; transition:all .2s ease; }
        .btn-primary { background:var(--primary); color:#fff; border:2px solid var(--primary); box-shadow:0 4px 18px rgba(74,29,150,.4); }
        .btn-primary:hover { background:var(--primary-light); border-color:var(--primary-light); transform:translateY(-2px); }
        .btn-outline { background:transparent; color:#fff; border:2px solid #fff; }
        .btn-outline:hover { background:#fff; color:var(--primary); transform:translateY(-2px); }
        .btn-slide {
            position:absolute; top:50%; transform:translateY(-50%); z-index:3;
            width:44px; height:44px; border-radius:50%; border:none; cursor:pointer;
            background:rgba(255,255,255,.2); color:#fff; font-size:1rem;
            backdrop-filter:blur(4px); transition:background .2s;
        }
        .btn-slide:hover { background:rgba(255,255,255,.35); }
        .btn-slide-prev { left:20px; }
        .btn-slide-next { right:20px; }
        .slide-dots { position:absolute; bottom:24px; left:50%; transform:translateX(-50%); z-index:3; display:flex; gap:8px; }
        .dot { width:10px; height:10px; border-radius:50%; border:2px solid rgba(255,255,255,.6); background:transparent; cursor:pointer; transition:all .3s; padding:0; }
        .dot.active { background:#fff; border-color:#fff; transform:scale(1.2); }

        /* ── Secciones ── */
        section { padding: 80px 0; }
        .section-header { text-align:center; margin-bottom:52px; }
        .section-header span { font-size:.8rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:var(--primary-light); }
        .section-header h2 { font-family:'Outfit',sans-serif; font-size:clamp(1.6rem,3vw,2.4rem); font-weight:800; color:var(--text); margin:8px 0 14px; }
        .section-header p { color:var(--text-muted); max-width:580px; margin:0 auto; line-height:1.7; }

        /* ── Cards de módulos ── */
        .roles-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:24px; }
        .modulos-grid {
            display:grid;
            grid-template-columns: repeat(4, 1fr);
            gap:24px;
        }
        @media (max-width: 1024px) { .modulos-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px)  { .modulos-grid { grid-template-columns: 1fr; } }
        .role-card {
            background:#fff; border-radius:16px; padding:28px 24px;
            border:1px solid var(--border);
            box-shadow:0 2px 16px rgba(74,29,150,.06);
            transition:transform .2s, box-shadow .2s;
            display:flex; flex-direction:column;
        }
        .role-card:hover { transform:translateY(-4px); box-shadow:0 8px 32px rgba(74,29,150,.12); }
        .role-icon { width:52px; height:52px; border-radius:14px; background:var(--primary-ultra); display:flex; align-items:center; justify-content:center; font-size:1.4rem; color:var(--primary); margin-bottom:16px; }
        .role-card h3 { font-family:'Outfit',sans-serif; font-weight:700; font-size:1.05rem; color:var(--text); margin-bottom:8px; }
        .role-card p { font-size:.88rem; color:var(--text-muted); line-height:1.65; }

        /* ── Stats ── */
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:24px; margin-top:40px; }
        .stat-item { text-align:center; }
        .stat-number { display:block; font-family:'Outfit',sans-serif; font-size:2.4rem; font-weight:900; color:var(--primary); }
        .stat-label { font-size:.82rem; color:var(--text-muted); font-weight:500; }

        /* ── Footer ── */
        .footer { background:#1E0747; color:rgba(255,255,255,.7); padding:60px 0 0; }
        .footer-grid { display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:40px; padding-bottom:40px; }
        .footer-brand p { font-size:.88rem; line-height:1.7; margin-top:14px; }
        .footer-title { font-family:'Outfit',sans-serif; font-weight:700; color:#fff; font-size:.95rem; margin-bottom:16px; }
        .footer-links { list-style:none; }
        .footer-links li { margin-bottom:8px; }
        .footer-links a { color:rgba(255,255,255,.6); text-decoration:none; font-size:.87rem; transition:color .2s; }
        .footer-links a:hover { color:#fff; }
        .footer-contact p { font-size:.85rem; margin-bottom:8px; display:flex; align-items:center; gap:8px; }
        .footer-contact i { color:var(--accent); width:16px; }
        .social-links { display:flex; gap:10px; margin-top:16px; }
        .social-link { width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,.1); display:flex; align-items:center; justify-content:center; color:rgba(255,255,255,.7); text-decoration:none; transition:all .2s; }
        .social-link:hover { background:var(--primary-light); color:#fff; }
        .footer-bottom { border-top:1px solid rgba(255,255,255,.08); padding:20px 0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; }
        .footer-bottom p { font-size:.82rem; }
        .footer-bottom i { color:var(--accent); }

        @media (max-width: 768px) {
            .footer-grid { grid-template-columns: 1fr; }
            .nav-links li:not(:last-child) { display: none; }
        }
    </style>
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar" id="navbar">
    <div class="container">
        <div class="navbar-inner">
            <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;"><img src="{{ asset('img/LogoSoloo.png') }}" alt="DisneyStock" style="height:52px;width:auto;object-fit:contain;"><span style="font-family:'Outfit',sans-serif;font-size:1.6rem;font-weight:800;color:#4A1D96;letter-spacing:-.5px;">DisneyStock</span></a><ul class="nav-links">
                <li><a href="#inicio"    class="nav-link">Inicio</a></li>
                <li><a href="#modulos"   class="nav-link">Módulos</a></li>
                <li><a href="#nosotros"  class="nav-link">Nosotros</a></li>
                <li><a href="#objetivos" class="nav-link">Objetivos</a></li>
                <li>
                    @auth
                        <a href="{{ route('dashboard') }}" class="nav-btn-ingresar">Panel</a>
                    @else
                        <a href="{{ route('login') }}" class="nav-btn-ingresar">Ingresar</a>
                    @endauth
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- CARRUSEL --}}
<section id="inicio" style="padding:0;">
    <div class="slide active">
        <img src="{{ asset('img/Imagen1.jpeg') }}" class="slide-bg" alt="Variedades Disney">
        <div class="slide-content">
            <h1>Bienvenido a Variedades Disney</h1>
            <p>DisneyStock reemplaza los cuadernos manuales y centraliza todo tu inventario y ventas en un solo lugar, desde cualquier navegador.</p>
            <div class="hero-btns">
                <a href="{{ route('login') }}" class="btn btn-primary">Ingresar al Sistema</a>
                <a href="#modulos" class="btn btn-outline">Ver Módulos</a>
            </div>
        </div>
    </div>

    <div class="slide">
        <img src="https://images.unsplash.com/photo-1611085583191-a3b181a88401?w=1600&q=80" class="slide-bg" alt="Accesorios">
        <div class="slide-content">
            <h1>Controla cada accesorio</h1>
            <p>Manillas, collares, aretes y más. Registra cada producto con su precio, cantidad y proveedor, y recibe alertas de stock bajo.</p>
            <div class="hero-btns">
                <a href="{{ route('login') }}" class="btn btn-primary">Ingresar al Sistema</a>
                <a href="#modulos" class="btn btn-outline">Ver Módulos</a>
            </div>
        </div>
    </div>

    <div class="slide">
        <img src="https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=1600&q=80" class="slide-bg" alt="Ropa">
        <div class="slide-content">
            <h1>Ropa y moda sin desorden</h1>
            <p>Lleva el inventario de vestidos y prendas de forma ordenada. Consulta cualquier producto en segundos y olvídate de los cuadernos.</p>
            <div class="hero-btns">
                <a href="{{ route('login') }}" class="btn btn-primary">Ingresar al Sistema</a>
                <a href="#modulos" class="btn btn-outline">Ver Módulos</a>
            </div>
        </div>
    </div>

    <div class="slide">
        <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1600&q=80" class="slide-bg" alt="Ventas">
        <div class="slide-content">
            <h1>Ventas registradas al instante</h1>
            <p>Cada venta descuenta el stock automáticamente. Consulta tus ventas del día, la semana o el mes con un solo clic.</p>
            <div class="hero-btns">
                <a href="{{ route('login') }}" class="btn btn-primary">Ingresar al Sistema</a>
                <a href="#modulos" class="btn btn-outline">Ver Módulos</a>
            </div>
        </div>
    </div>

    <button class="btn-slide btn-slide-prev" onclick="changeSlide(-1)"><i class="fas fa-chevron-left"></i></button>
    <button class="btn-slide btn-slide-next" onclick="changeSlide(1)"><i class="fas fa-chevron-right"></i></button>
    <div class="slide-dots" id="slideDots"></div>
</section>

{{-- MÓDULOS --}}
<section id="modulos" style="scroll-margin-top:92px; padding-top:60px; padding-bottom:80px;">
    <div class="container">
        <div style="text-align:center; margin-bottom:48px; padding-top:8px;">
            <p style="font-size:.8rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:#7C3AED; margin-bottom:10px;">Funcionalidades</p>
            <h2 style="font-family:'Outfit',sans-serif; font-size:clamp(1.8rem,3vw,2.6rem); font-weight:800; color:#1E1B4B; margin-bottom:14px; line-height:1.2;">Módulos del sistema</h2>
            <p style="color:#6B7280; max-width:560px; margin:0 auto; line-height:1.7; font-size:.97rem;">Todo lo que necesitas para controlar el inventario y las ventas de tu tienda en un solo lugar.</p>
        </div>
        <div class="modulos-grid">
            <div class="role-card">
                <div class="role-icon"><i class="fas fa-boxes-stacked"></i></div>
                <h3>Inventario</h3>
                <p>Registra y consulta productos con nombre, categoría, cantidad, precio, fecha de ingreso y proveedor. Alertas automáticas de stock bajo.</p>
            </div>
            <div class="role-card">
                <div class="role-icon"><i class="fas fa-cash-register"></i></div>
                <h3>Ventas</h3>
                <p>Registra ventas diarias y el sistema descuenta el stock automáticamente. Consulta el historial de ventas por fecha o producto.</p>
            </div>
            <div class="role-card">
                <div class="role-icon"><i class="fas fa-chart-bar"></i></div>
                <h3>Reportes</h3>
                <p>Reportes de ventas por período, inventario valorizado y productos con bajo stock. Información clara para tomar mejores decisiones.</p>
            </div>
            <div class="role-card">
                <div class="role-icon"><i class="fas fa-user-shield"></i></div>
                <h3>Control de Roles</h3>
                <p>Dos niveles de acceso: <strong>Administrador</strong> con control total y <strong>Empleado</strong> con permisos limitados.</p>
            </div>
            <div class="role-card">
                <div class="role-icon"><i class="fas fa-bell"></i></div>
                <h3>Alertas de Stock</h3>
                <p>El sistema te avisa automáticamente cuando un producto llega al mínimo definido, para que hagas el pedido a tiempo.</p>
            </div>
            <div class="role-card">
                <div class="role-icon"><i class="fas fa-tags"></i></div>
                <h3>Categorías</h3>
                <p>Organiza tus productos en categorías: Manillas, Vestidos, Accesorios, Bolsos, Zapatos y más. Filtra y encuentra cualquier artículo rápido.</p>
            </div>
            <div class="role-card">
                <div class="role-icon"><i class="fas fa-users"></i></div>
                <h3>Empleados</h3>
                <p>Registra empleados con usuario y contraseña, edita sus datos, desactívalos o restablece su contraseña cuando lo necesiten.</p>
            </div>
            <div class="role-card">
                <div class="role-icon"><i class="fas fa-receipt"></i></div>
                <h3>Comprobantes de Venta</h3>
                <p>Cada venta genera un comprobante con productos, cantidades y total, para el cliente y para tu control.</p>
            </div>
        </div>
    </div>
</section>

{{-- NOSOTROS --}}
<section id="nosotros" style="background:var(--primary-ultra);">
    <div class="container">
        <div class="section-header">
            <span>Sobre el Proyecto</span>
            <h2>DisneyStock para Variedades Disney</h2>
            <p>Un sistema desarrollado a la medida del negocio, con metodología en cascada y enfoque en seguridad de datos conforme a la Ley 1581 de 2012.</p>
        </div>
        <div class="roles-grid">
            <div class="role-card" style="border-top:4px solid var(--primary-light);">
                <div class="role-icon"><i class="fas fa-bullseye"></i></div>
                <h3 style="font-size:1.05rem;">Misión</h3>
                <p style="font-size:.88rem;">Automatizar el inventario y el registro de ventas de Variedades Disney, reemplazando los cuadernos manuales por un sistema digital que centraliza toda la información, reduce errores y permite consultar datos en segundos.</p>
            </div>
            <div class="role-card" style="border-top:4px solid var(--disney-300, #A78BFA);">
                <div class="role-icon"><i class="fas fa-eye"></i></div>
                <h3 style="font-size:1.05rem;">Visión</h3>
                <p style="font-size:.88rem;">Ser la herramienta de referencia para la gestión de Variedades Disney, escalable hacia otros locales del Huila, reconocida por su facilidad de uso, seguridad y confiabilidad.</p>
            </div>
        </div>
        <div class="stats-grid">
            <div class="stat-item"><span class="stat-number">0</span><span class="stat-label">Cuadernos Manuales</span></div>
            <div class="stat-item"><span class="stat-number">100%</span><span class="stat-label">Web</span></div>
            <div class="stat-item"><span class="stat-number">2</span><span class="stat-label">Roles de Acceso</span></div>
            <div class="stat-item"><span class="stat-number">8</span><span class="stat-label">Módulos</span></div>
        </div>
    </div>
</section>

{{-- OBJETIVOS --}}
<section id="objetivos">
    <div class="container">
        <div class="section-header">
            <span>Objetivos del Sistema</span>
            <h2>¿Por qué DisneyStock?</h2>
            <p>Diseñado bajo metodología en cascada, con control de acceso por roles y pensado para el día a día de Variedades Disney en el Huila, Colombia.</p>
        </div>
        <div class="roles-grid">
            <div class="role-card">
                <div class="role-icon"><i class="fas fa-bolt"></i></div>
                <h3>Automatización</h3>
                <p>Cada venta descuenta el stock sola. El total de la venta y las alertas de stock bajo se calculan automáticamente.</p>
            </div>
            <div class="role-card">
                <div class="role-icon"><i class="fas fa-shield-halved"></i></div>
                <h3>Seguridad</h3>
                <p>Contraseñas cifradas, control de acceso por roles y cumplimiento de la <strong>Ley 1581 de 2012</strong> de protección de datos.</p>
            </div>
            <div class="role-card">
                <div class="role-icon"><i class="fas fa-chart-line"></i></div>
                <h3>Trazabilidad</h3>
                <p>Cada entrada de mercancía y cada venta queda registrada con su fecha, para que puedas consultar el historial cuando lo necesites.</p>
            </div>
        </div>
    </div>
</section>

{{-- FOOTER --}}
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
                    <img src="{{ asset('img/BlancoSolo.png') }}" alt="DisneyStock" style="height:48px;width:auto;object-fit:contain;">
                    <span style="font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:800;color:#fff;">DisneyStock</span>
                </a>
                <p>Sistema de gestión de inventario y ventas para Variedades Disney. Datos seguros, decisiones inteligentes.</p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            <div>
                <h4 class="footer-title">Navegación</h4>
                <ul class="footer-links">
                    <li><a href="#inicio">Inicio</a></li>
                    <li><a href="#modulos">Módulos</a></li>
                    <li><a href="#nosotros">Nosotros</a></li>
                    <li><a href="#objetivos">Objetivos</a></li>
                </ul>
            </div>
            <div>
                <h4 class="footer-title">Legal</h4>
                <ul class="footer-links">
                    <li><a href="#" onclick="abrirModalPrivacidad(); return false;">Privacidad de Datos</a></li>
                    <li><a href="#" onclick="abrirModalPrivacidad(); return false;">Ley 1581 de 2012</a></li>
                    <li><a href="#">Términos de Uso</a></li>
                    <li><a href="#">Soporte</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h4 class="footer-title">Desarrolladora</h4>
                <p><i class="fas fa-user" style="color:#A78BFA;"></i> Heidy Johanna Reyes Quesada</p>
                <p><i class="fas fa-location-dot" style="color:#A78BFA;"></i> Huila, Colombia</p>
                <p><i class="fas fa-store" style="color:#A78BFA;"></i> Variedades Disney</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} DisneyStock — Variedades Disney. Todos los derechos reservados.</p>
            <p>Desarrollado con <i class="fas fa-heart" style="color:#A78BFA;"></i> en el Huila, Colombia.</p>
        </div>
    </div>
</footer>

<script>
    let current = 0;
    const slides = document.querySelectorAll('.slide');
    const dotsContainer = document.getElementById('slideDots');

    slides.forEach((_, i) => {
        const dot = document.createElement('button');
        dot.className = 'dot' + (i === 0 ? ' active' : '');
        dot.addEventListener('click', () => goTo(i));
        dotsContainer.appendChild(dot);
    });

    function getDots() { return document.querySelectorAll('.dot'); }

    function goTo(index) {
        slides[current].classList.remove('active');
        getDots()[current].classList.remove('active');
        current = (index + slides.length) % slides.length;
        slides[current].classList.add('active');
        getDots()[current].classList.add('active');
    }

    function changeSlide(dir) { goTo(current + dir); }

    setInterval(() => changeSlide(1), 6000);

    window.addEventListener('scroll', () => {
        document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 50);
    });
</script>
{{-- MODAL PRIVACIDAD --}}
<div id="modalPrivacidad" style="display:none; position:fixed; inset:0; z-index:2000; align-items:center; justify-content:center; padding:24px;">
    <div style="position:absolute; inset:0; background:rgba(30,7,71,.55); backdrop-filter:blur(4px);" onclick="cerrarModalPrivacidad()"></div>
    <div style="position:relative; background:#fff; border-radius:20px; max-width:540px; width:100%; padding:40px 36px; box-shadow:0 20px 60px rgba(74,29,150,.2);">
        <button onclick="cerrarModalPrivacidad()" style="position:absolute; top:16px; right:18px; background:none; border:none; font-size:1.3rem; color:#9CA3AF; cursor:pointer; line-height:1;">
            <i class="fas fa-xmark"></i>
        </button>
        <div style="width:48px; height:48px; border-radius:14px; background:#EDE9FE; display:flex; align-items:center; justify-content:center; font-size:1.3rem; color:#7C3AED; margin-bottom:18px;">
            <i class="fas fa-shield-halved"></i>
        </div>
        <h2 style="font-family:'Outfit',sans-serif; font-size:1.4rem; font-weight:800; color:#1E1B4B; margin-bottom:6px;">Privacidad de Datos</h2>
        <p style="font-size:.8rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#7C3AED; margin-bottom:20px;">Ley 1581 de 2012 — Colombia</p>
        <div style="font-size:.92rem; color:#374151; line-height:1.75;">
            <p style="margin-bottom:14px;">
                <strong>DisneyStock</strong> recopila y trata únicamente los datos necesarios para el funcionamiento del sistema: nombre de usuario, contraseña cifrada y rol de acceso.
            </p>
            <p style="margin-bottom:14px;">
                Esta información se usa exclusivamente para autenticar usuarios, registrar ventas y movimientos de inventario dentro de la plataforma. <strong>No se comparte con terceros bajo ninguna circunstancia.</strong>
            </p>
            <p style="margin-bottom:14px;">
                El tratamiento de datos personales se realiza conforme a la <strong>Ley 1581 de 2012</strong> de Protección de Datos Personales de Colombia y sus decretos reglamentarios.
            </p>
            <p>
                Los usuarios tienen derecho a conocer, actualizar y rectificar sus datos. Para ejercer estos derechos, comuníquese con la administradora del sistema.
            </p>
        </div>
        <button onclick="cerrarModalPrivacidad()" style="margin-top:28px; width:100%; padding:12px; background:linear-gradient(135deg,#4A1D96,#7C3AED); color:#fff; border:none; border-radius:12px; font-family:'Outfit',sans-serif; font-size:.95rem; font-weight:700; cursor:pointer;">
            Entendido
        </button>
    </div>
</div>

<script>
    function abrirModalPrivacidad() {
        const m = document.getElementById('modalPrivacidad');
        m.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function cerrarModalPrivacidad() {
        const m = document.getElementById('modalPrivacidad');
        m.style.display = 'none';
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModalPrivacidad(); });
</script>

</body>
</html>
