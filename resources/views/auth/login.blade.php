<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — DisneyStock</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #1E0747;
            overflow: hidden;
        }

        /* ── Panel izquierdo (decorativo) ── */
        .login-left {
            flex: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px;
            overflow: hidden;
        }
        .login-left::before {
            content: '';
            position: absolute; inset: 0;
            background: url('{{ asset("img/Imagen1.jpeg") }}') center/cover no-repeat;
            opacity: .18;
        }
        .login-left::after {
            content: '';
            position: absolute; inset: 0;
            background: rgba(44,10,100,.70);
        }
        .login-left-content {
            position: relative; z-index: 1;
            text-align: center; color: #fff;
        }
        .login-left-content img { height: 90px; width: auto; object-fit: contain; margin-bottom: 24px; filter: brightness(0) invert(1); }
        .login-left-content h1 { font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 900; margin-bottom: 12px; letter-spacing: -.5px; }
        .login-left-content p { font-size: .95rem; color: rgba(255,255,255,.7); line-height: 1.7; max-width: 340px; margin: 0 auto 36px; }
        .feature-list { list-style: none; text-align: left; }
        .feature-list li {
            display: flex; align-items: center; gap: 12px;
            font-size: .87rem; color: rgba(255,255,255,.8);
            padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .feature-list li:last-child { border-bottom: none; }
        .feature-list i { color: #A78BFA; width: 18px; text-align: center; }

        /* ── Panel derecho (formulario) ── */
        .login-right {
            width: 480px; flex-shrink: 0;
            background: #fff;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 56px 48px;
        }
        .login-logo-mobile { display: none; }
        .login-form-header { text-align: center; margin-bottom: 36px; }
        .login-form-header h2 { font-family: 'Outfit', sans-serif; font-size: 1.75rem; font-weight: 800; color: #1E1B4B; }
        .login-form-header p { font-size: .88rem; color: #6B7280; margin-top: 6px; }

        /* ── Formulario ── */
        .form-group { margin-bottom: 20px; width: 100%; }
        .form-label { display: block; font-size: .83rem; font-weight: 600; color: #374151; margin-bottom: 7px; letter-spacing: .02em; }
        .input-wrap { position: relative; width: 100%; }
        .input-wrap i {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: #9CA3AF; font-size: .9rem; pointer-events: none;
        }
        .form-input {
            width: 100%; padding: 12px 42px 12px 40px;
            border: 1.5px solid #E5E7EB; border-radius: 10px;
            font-size: .93rem; color: #1E1B4B; font-family: 'Inter', sans-serif;
            transition: border-color .2s, box-shadow .2s;
            background: #FAFAFA; box-sizing: border-box;
        }
        .form-input:focus { outline: none; border-color: #7C3AED; box-shadow: 0 0 0 3px rgba(124,58,237,.12); background: #fff; }
        .form-input.is-invalid { border-color: #EF4444; }
        .toggle-pass {
            position: absolute; right: 0; top: 0; bottom: 0;
            width: 42px;
            display: flex; align-items: center; justify-content: center;
            color: #9CA3AF; cursor: pointer; font-size: .9rem;
            background: none; border: none; border-radius: 0 10px 10px 0;
        }
        .toggle-pass:hover { color: #7C3AED; }
        #password { padding-right: 42px; }
        .invalid-feedback { font-size: .78rem; color: #EF4444; margin-top: 5px; display: flex; align-items: center; gap: 5px; }

        /* ── Remember + Forgot ── */
        .form-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .checkbox-wrap { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: .84rem; color: #6B7280; }
        .checkbox-wrap input { accent-color: #7C3AED; width: 15px; height: 15px; cursor: pointer; }
        .forgot-link { font-size: .83rem; color: #7C3AED; text-decoration: none; font-weight: 500; }
        .forgot-link:hover { color: #4A1D96; text-decoration: underline; }

        /* ── Botón submit ── */
        .btn-login {
            width: 100%; padding: 13px;
            background: #4A1D96;
            color: #fff; border: 2px solid #4A1D96; border-radius: 12px;
            font-size: .97rem; font-weight: 700; font-family: 'Outfit', sans-serif;
            cursor: pointer; transition: all .2s ease;
            box-shadow: 0 4px 18px rgba(74,29,150,.35);
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover { background: #7C3AED; border-color: #7C3AED; transform: translateY(-2px); box-shadow: 0 8px 28px rgba(74,29,150,.45); }
        .btn-login:active { transform: translateY(0); }

        /* ── Alerta de error de sesión ── */
        .alert-error {
            width: 100%; background: #FEE2E2; border: 1px solid #FCA5A5;
            color: #991B1B; border-radius: 10px;
            padding: 12px 16px; font-size: .87rem;
            display: flex; align-items: center; gap: 10px; margin-bottom: 20px;
        }

        /* ── Volver a inicio ── */
        .back-link { margin-top: 28px; font-size: .83rem; color: #9CA3AF; text-align: center; }
        .back-link a { color: #7C3AED; text-decoration: none; font-weight: 500; }
        .back-link a:hover { text-decoration: underline; }

        @media (max-width: 768px) {
            body { flex-direction: column; overflow: auto; }
            .login-left { display: none; }
            .login-right { width: 100%; min-height: 100vh; padding: 40px 28px; }
            .login-logo-mobile { display: flex; flex-direction: column; align-items: center; gap: 10px; margin-bottom: 32px; }
            .login-logo-mobile img { height: 60px; }
            .login-logo-mobile span { font-family: 'Outfit', sans-serif; font-size: 1.6rem; font-weight: 800; color: #4A1D96; }
        }
    </style>
</head>
<body>

{{-- Panel izquierdo --}}
<div class="login-left">
    <div class="login-left-content">
        <img src="{{ asset('img/BlancoSolo.png') }}" alt="DisneyStock">
        <h1>DisneyStock</h1>
        <p>Sistema de gestión de inventario y ventas para Variedades Disney. Toma decisiones con datos reales.</p>
        <ul class="feature-list">
            <li><i class="fas fa-boxes-stacked"></i> Control de inventario en tiempo real</li>
            <li><i class="fas fa-cash-register"></i> Registro de ventas automático</li>
            <li><i class="fas fa-bell"></i> Alertas de stock bajo</li>
            <li><i class="fas fa-chart-bar"></i> Reportes por período</li>
            <li><i class="fas fa-shield-halved"></i> Acceso seguro por roles</li>
        </ul>
    </div>
</div>

{{-- Panel derecho — formulario --}}
<div class="login-right">

    {{-- Logo para móvil --}}
    <div class="login-logo-mobile">
        <img src="{{ asset('img/LogoSoloo.png') }}" alt="DisneyStock">
        <span>DisneyStock</span>
    </div>

    <div class="login-form-header">
        <h2>Iniciar sesión</h2>
        <p>Ingresa tus credenciales para acceder al panel</p>
    </div>

    {{-- Error general de sesión --}}
    @if(session('error'))
        <div class="alert-error">
            <i class="fas fa-circle-xmark"></i>
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" style="width:100%;">
        @csrf

        {{-- Usuario --}}
        <div class="form-group">
            <label class="form-label" for="usuario">Usuario</label>
            <div class="input-wrap">
                <i class="fas fa-user"></i>
                <input
                    type="text"
                    id="usuario"
                    name="usuario"
                    class="form-input {{ $errors->has('usuario') ? 'is-invalid' : '' }}"
                    value="{{ old('usuario') }}"
                    placeholder="Tu nombre de usuario"
                    autocomplete="username"
                    autofocus
                >
            </div>
            @error('usuario')
                <div class="invalid-feedback"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
            @enderror
        </div>

        {{-- Contraseña --}}
        <div class="form-group">
            <label class="form-label" for="password">Contraseña</label>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    placeholder="Tu contraseña"
                    autocomplete="current-password"
                >
                <button type="button" class="toggle-pass" onclick="togglePassword()" id="toggleBtn">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
            @enderror
        </div>

        {{-- Recuérdame --}}
        <div class="form-row">
            <label class="checkbox-wrap">
                <input type="checkbox" name="remember"> Recordarme
            </label>
            <a href="#" class="forgot-link" onclick="document.getElementById('modalOlvidaste').style.display='flex'; return false;">¿Olvidaste tu contraseña?</a>
        </div>

        <button type="submit" class="btn-login">
            <i class="fas fa-right-to-bracket"></i> Ingresar al Sistema
        </button>
    </form>

    <div class="back-link">
        <a href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> Volver al inicio</a>
    </div>
</div>

{{-- Modal: contraseña olvidada --}}
<div id="modalOlvidaste" style="display:none; position:fixed; inset:0; z-index:2000; align-items:center; justify-content:center; padding:24px;">
    <div style="position:absolute; inset:0; background:rgba(30,7,71,.55);" onclick="this.parentElement.style.display='none'"></div>
    <div style="position:relative; background:#fff; border-radius:20px; max-width:420px; width:100%; padding:36px 32px; box-shadow:0 20px 60px rgba(74,29,150,.2); text-align:center;">
        <button onclick="this.closest('#modalOlvidaste').style.display='none'" style="position:absolute; top:14px; right:16px; background:none; border:none; font-size:1.2rem; color:#9CA3AF; cursor:pointer;">
            <i class="fas fa-xmark"></i>
        </button>
        <div style="width:52px; height:52px; border-radius:14px; background:#EDE9FE; display:flex; align-items:center; justify-content:center; font-size:1.3rem; color:#7C3AED; margin:0 auto 18px;">
            <i class="fas fa-key"></i>
        </div>
        <h3 style="font-family:'Outfit',sans-serif; font-size:1.2rem; font-weight:800; color:#1E1B4B; margin-bottom:10px;">¿Olvidaste tu contraseña?</h3>
        <p style="font-size:.9rem; color:#6B7280; line-height:1.7; margin-bottom:24px;">
            Comunícate con el administrador para restablecer tu contraseña.
        </p>
        <button onclick="this.closest('#modalOlvidaste').style.display='none'" style="width:100%; padding:11px; background:#4A1D96; color:#fff; border:2px solid #4A1D96; border-radius:12px; font-family:'Outfit',sans-serif; font-size:.95rem; font-weight:700; cursor:pointer; transition:background .2s;" onmouseover="this.style.background='#7C3AED';this.style.borderColor='#7C3AED'" onmouseout="this.style.background='#4A1D96';this.style.borderColor='#4A1D96'">
            Entendido
        </button>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') document.getElementById('modalOlvidaste').style.display = 'none';
    });
</script>
</body>
</html>
