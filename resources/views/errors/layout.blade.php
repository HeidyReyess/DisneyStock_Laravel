<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    {{-- Anti-parpadeo: aplicar modo oscuro de inmediato si está activo --}}
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
    <title>@yield('title', 'Error') — DisneyStock</title>
    <link rel="icon" type="image/png" href="{{ asset('img/LogoSoloo.png') }}">

    {{-- Fuentes --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg-page: #F5F3FF;
            --bg-card: #ffffff;
            --border-card: #EDE9FE;
            --text-heading: #1E1B4B;
            --text-body: #4B5563;
            --text-muted: #6B7280;
            --primary: #4A1D96;
            --primary-hover: #7C3AED;
            --icon-bg: #EDE9FE;
            --icon-color: #7C3AED;
            --shadow: 0 20px 50px rgba(74, 29, 150, 0.10);
        }

        html.dark {
            --bg-page: #0F0A1C;
            --bg-card: #181129;
            --border-card: #2F1E52;
            --text-heading: #F5F3FF;
            --text-body: #D1D5DB;
            --text-muted: #A89BC4;
            --primary: #7C3AED;
            --primary-hover: #8B5CF6;
            --icon-bg: #28174E;
            --icon-color: #DDD6FE;
            --shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-page);
            color: var(--text-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            transition: background .2s ease, color .2s ease;
            position: relative;
        }

        /* Botón de alternar modo oscuro en esquina */
        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            color: var(--icon-color);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,.06);
            transition: all .2s;
        }
        .theme-toggle:hover {
            transform: scale(1.05);
            background: var(--border-card);
        }

        .error-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 20px;
            box-shadow: var(--shadow);
            max-width: 480px;
            width: 100%;
            padding: 44px 36px;
            text-align: center;
            transition: background .2s ease, border-color .2s ease;
        }

        .brand-header {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }
        .brand-header img {
            height: 38px;
            width: auto;
            object-fit: contain;
        }
        .logo-dark { display: none; }
        html.dark .logo-light { display: none; }
        html.dark .logo-dark { display: inline-block; }

        .brand-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-heading);
            letter-spacing: -.5px;
        }

        .icon-wrap {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--icon-bg);
            color: var(--icon-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin-bottom: 18px;
        }

        .error-code {
            font-family: 'Outfit', sans-serif;
            font-size: 4.8rem;
            font-weight: 900;
            line-height: 1;
            color: var(--primary);
            letter-spacing: -2px;
            margin-bottom: 12px;
        }

        .error-message {
            font-size: 1.05rem;
            color: var(--text-heading);
            font-weight: 600;
            line-height: 1.5;
            margin-bottom: 28px;
        }

        .btn-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 28px;
            border-radius: 12px;
            background: var(--primary);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-size: .95rem;
            font-weight: 700;
            text-decoration: none;
            transition: all .2s ease;
            box-shadow: 0 4px 14px rgba(74, 29, 150, 0.25);
        }
        .btn-home:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(124, 58, 237, 0.35);
            color: #ffffff;
        }

        .footer-note {
            margin-top: 24px;
            font-size: .78rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

    {{-- Botón Modo Oscuro --}}
    <button type="button" class="theme-toggle" id="btnThemeToggle" title="Cambiar tema" onclick="toggleTheme()" aria-label="Cambiar tema">
        <i class="fas fa-moon" id="themeIcon"></i>
    </button>

    <div class="error-card">
        {{-- Logo DisneyStock --}}
        <div class="brand-header">
            <img src="{{ asset('img/LogoSoloo.png') }}" alt="DisneyStock" class="logo-light">
            <img src="{{ asset('img/BlancoSolo.png') }}" alt="DisneyStock" class="logo-dark">
            <span class="brand-title">DisneyStock</span>
        </div>

        {{-- Ícono representativo --}}
        <div>
            <div class="icon-wrap">
                <i class="@yield('icon')"></i>
            </div>
        </div>

        {{-- Código de error --}}
        <div class="error-code">@yield('code')</div>

        {{-- Mensaje en español --}}
        <p class="error-message">@yield('message')</p>

        {{-- Botón Volver al inicio --}}
        @php
            $urlInicio = route('login');
            try {
                if (\Illuminate\Support\Facades\Auth::check()) {
                    $urlInicio = route('dashboard');
                }
            } catch (\Throwable $e) {
                $urlInicio = url('/');
            }
        @endphp
        <a href="{{ $urlInicio }}" class="btn-home">
            <i class="fas fa-arrow-left"></i>
            <span>Volver al inicio</span>
        </a>

        <div class="footer-note">
            DisneyStock &copy; {{ date('Y') }}
        </div>
    </div>

    <script>
        function updateThemeIcon() {
            var icon = document.getElementById('themeIcon');
            var isDark = document.documentElement.classList.contains('dark');
            if (icon) {
                icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
            }
        }
        function toggleTheme() {
            var isDark = document.documentElement.classList.toggle('dark');
            try {
                localStorage.setItem('darkMode', isDark ? '1' : '0');
            } catch (e) {}
            updateThemeIcon();
        }
        updateThemeIcon();
    </script>
</body>
</html>

