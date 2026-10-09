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
            background: rgba(255,255,255,.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(221,214,254,.5);
            transition: box-shadow .3s ease;
        }
        .navbar.scrolled { box-shadow: 0 4px 24px rgba(74,29,150,.12); }
        .navbar-inner { display:flex; align-items:center; justify-content:space-between; height:var(--navbar-h); }
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
        .slide::after { content:''; position:absolute; inset:0; background:linear-gradient(135deg, rgba(30,7,71,.75) 0%, rgba(74,29,150,.5) 100%); }
        .slide-content {
            position:absolute; inset:0; z-index:2;
            display:flex; flex-direction:column; align-items:center; justify-content:center;
            text-align:center; padding:0 24px; padding-top:var(--navbar-h);
        }
        .slide-content h1 { font-family:'Outfit',sans-serif; font-size:clamp(2rem,5vw,3.5rem); font-weight:900; color:#fff; margin-bottom:16px; line-height:1.15; }
        .slide-content p { font-size:clamp(.95rem,2vw,1.15rem); color:rgba(255,255,255,.85); max-width:620px; margin-bottom:32px; line-height:1.7; }
        .hero-btns { display:flex; gap:14px; flex-wrap:wrap; justify-content:center; }
        .btn { padding:13px 28px; border-radius:12px; font-weight:700; font-size:.95rem; text-decoration:none; transition:all .2s ease; }
        .btn-primary { background:var(--primary); color:#fff; box-shadow:0 4px 18px rgba(74,29,150,.4); }
        .btn-primary:hover { background:var(--primary-light); transform:translateY(-2px); }
        .btn-accent { background:var(--accent); color:#1E1B4B; box-shadow:0 4px 18px rgba(245,158,11,.35); }
        .btn-accent:hover { background:var(--accent-dark); transform:translateY(-2px); }
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
        .role-card {
            background:#fff; border-radius:16px; padding:28px 24px;
            border:1px solid var(--border);
            box-shadow:0 2px 16px rgba(74,29,150,.06);
            transition:transform .2s, box-shadow .2s;
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
            <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
                <img src="{{ asset('img/LogoSoloo.png') }}" alt="DisneyStock" style="height:52px;width:auto;object-fit:contain;">
                <span style="font-family:'Outfit',sans-serif;font-size:1.6rem;font-weight:800;color:#4A1D96;letter-spacing:-.5px;">DisneyStock</span>
            </a>
            <ul class="nav-links">
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
                <a href="#modulos" class="btn btn-accent">Ver Módulos</a>
            </div>
        </div>
    </div>

    <div class="slide">
        <img src="https://images.unsplash.com/photo-1611085583191-a3b181a88401?w=1600&q=80" class="slide-bg" alt="Accesorios">
        <div class="slide-content">
            <h1>Controla cada Accesorio</h1>
            <p>Manillas, collares, aretes y más — registra cada producto con su precio, cantidad y proveedor. Recibe alertas antes de quedarte sin stock.</p>
            <div class="hero-btns">
                <a href="{{ route('login') }}" class="btn btn-primary">Ingresar al Sistema</a>
                <a href="#modulos" class="btn btn-accent">Ver Módulos</a>
            </div>
        </div>
    </div>

    <div class="slide">
        <img src="https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=1600&q=80" class="slide-bg" alt="Ropa">
        <div class="slide-content">
            <h1>Ropa y Moda sin Desorden</h1>
            <p>Lleva el inventario de vestidos y prendas de forma ordenada. Consulta cualquier producto en segundos y olvídate de los cuadernos.</p>
            <div class="hero-btns">
                <a href="{{ route('login') }}" class="btn btn-primary">Ingresar al Sistema</a>
                <a href="#modulos" class="btn btn-accent">Ver Módulos</a>
            </div>
        </div>
    </div>

    <div class="slide">
        <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1600&q=80" class="slide-bg" alt="Ventas">
        <div class="slide-content">
            <h1>Ventas Registradas al Instante</h1>
            <p>Cada venta descuenta el stock automáticamente. Consulta tus ventas del día, la semana o el mes con un solo clic.</p>
            <div class="hero-btns">
                <a href="{{ route('login') }}" class="btn btn-primary">Ingresar al Sistema</a>
                <a href="#modulos" class="btn btn-accent">Ver Módulos</a>
            </div>
        </div>
    </div>

    <button class="btn-slide btn-slide-prev" onclick="changeSlide(-1)"><i class="fas fa-chevron-left"></i></button>
    <button class="btn-slide btn-slide-next" onclick="changeSlide(1)"><i class="fas fa-chevron-right"></i></button>
    <div class="slide-dots" id="slideDots"></div>
</section>

{{-- MÓDULOS --}}
<section id="modulos">
    <div class="container">
        <div class="section-header">
            <span>Funcionalidades</span>
            <h2>Todo lo que Variedades Disney Necesita</h2>
            <p>DisneyStock digitaliza los procesos clave de tu negocio — inventario, ventas, clientes y reportes — en una sola plataforma web.</p>
        </div>
        <div class="roles-grid">
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
                <h3>Misión</h3>
                <p>Automatizar el inventario y el registro de ventas de Variedades Disney, reemplazando los cuadernos manuales por un sistema digital que centraliza toda la información, reduce errores y permite consultar datos en segundos.</p>
            </div>
            <div class="role-card" style="border-top:4px solid var(--accent-dark);">
                <div class="role-icon"><i class="fas fa-eye"></i></div>
                <h3>Visión</h3>
                <p>Ser la herramienta de referencia para la gestión de Variedades Disney, escalable hacia otros locales del Huila, reconocida por su facilidad de uso, seguridad y confiabilidad.</p>
            </div>
        </div>
        <div class="stats-grid">
            <div class="stat-item"><span class="stat-number">0</span><span class="stat-label">Cuadernos Manuales</span></div>
            <div class="stat-item"><span class="stat-number">100%</span><span class="stat-label">Web & Seguro</span></div>
            <div class="stat-item"><span class="stat-number">2</span><span class="stat-label">Roles de Acceso</span></div>
            <div class="stat-item"><span class="stat-number">10</span><span class="stat-label">Categorías</span></div>
        </div>
    </div>
</section>

{{-- OBJETIVOS --}}
<section id="objetivos">
    <div class="container">
        <div class="section-header">
            <span>Objetivos del Sistema</span>
            <h2>¿Por qué DisneyStock?</h2>
            <p>Diseñado bajo metodología en cascada, con seguridad robusta y pensado para el día a día de Variedades Disney en el Huila, Colombia.</p>
        </div>
        <div class="roles-grid">
            <div class="role-card">
                <div class="role-icon" style="color:var(--sky);background:#EFF6FF;"><i class="fas fa-bolt"></i></div>
                <h3>Automatización</h3>
                <p>Cada venta actualiza el inventario sola. Los cálculos de totales, descuentos y alertas de stock se hacen automáticamente.</p>
            </div>
            <div class="role-card">
                <div class="role-icon" style="color:var(--sky);background:#EFF6FF;"><i class="fas fa-shield-halved"></i></div>
                <h3>Seguridad</h3>
                <p>Contraseñas cifradas, control de roles, monitoreo de actividad y cumplimiento de la <strong>Ley 1581 de 2012</strong> de protección de datos.</p>
            </div>
            <div class="role-card">
                <div class="role-icon" style="color:var(--sky);background:#EFF6FF;"><i class="fas fa-chart-line"></i></div>
                <h3>Trazabilidad</h3>
                <p>Cada movimiento de inventario y cada venta queda registrado con fecha y usuario, permitiendo auditorías completas.</p>
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
                    <li><a href="#">Privacidad de Datos</a></li>
                    <li><a href="#">Ley 1581 de 2012</a></li>
                    <li><a href="#">Términos de Uso</a></li>
                    <li><a href="#">Soporte</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h4 class="footer-title">Desarrolladora</h4>
                <p><i class="fas fa-user"></i> Heidy Johanna Reyes Quesada</p>
                <p><i class="fas fa-location-dot"></i> Huila, Colombia</p>
                <p><i class="fas fa-store"></i> Variedades Disney</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} DisneyStock — Variedades Disney. Todos los derechos reservados.</p>
            <p>Desarrollado con <i class="fas fa-heart"></i> en el Huila, Colombia.</p>
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
</body>
</html>
