
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión | Bella Flor POS</title>

    <style>
        :root {
            --oscuro: #593B4B;
            --medio: #D77D9D;
            --acento: #D77D9D;
            --suave: #E9A8BD;
            --durazno: #F8DDE7;
            --crema: #FFF8F5;

            --texto: #593B4B;
            --texto-2: #806A76;
            --borde: #F0DDE5;
            --error: #C0344F;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: Inter, "Segoe UI", system-ui, Arial, sans-serif;
            color: var(--texto);
            background:
                radial-gradient(900px 500px at 15% 0%,
                rgba(233, 168, 189, .48), transparent 60%),
                var(--crema);
            -webkit-font-smoothing: antialiased;
        }

        .shell {
            display: grid;
            grid-template-columns: 390px minmax(0, 1fr);
            width: min(1080px, 100%);
            min-height: 620px;
            border-radius: 22px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 30px 80px rgba(89, 59, 75, .16);
        }

        /* PANEL IZQUIERDO */

        .panel-login {
            display: flex;
            flex-direction: column;
            padding: 38px 38px 30px;
            background: #fff;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-name {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -.2px;
        }

        .brand-sub {
            margin-top: 3px;
            font-size: 11px;
            color: var(--texto-2);
        }

        .avatar {
            display: grid;
            place-items: center;
            width: 78px;
            height: 78px;
            margin: 34px auto 10px;
            border-radius: 50%;
            background: var(--medio);
            color: #fff;
            box-shadow: 0 10px 24px rgba(215, 125, 157, .28);
        }

        .avatar svg {
            width: 38px;
            height: 38px;
        }

        .titulo {
            text-align: center;
            font-size: 19px;
            font-weight: 700;
            letter-spacing: -.3px;
        }

        .subtitulo {
            margin: 6px 0 24px;
            text-align: center;
            font-size: 13px;
            color: var(--texto-2);
        }

        .aviso {
            margin-bottom: 14px;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 12.5px;
            line-height: 1.45;
        }

        .aviso.ok {
            background: #e8f5ee;
            color: #2f855a;
        }

        .aviso.error {
            background: #fdecef;
            color: var(--error);
        }

        .campo {
            position: relative;
            margin-top: 14px;
        }

        .campo input[type="email"],
        .campo input[type="password"],
        .campo input[type="text"] {
            width: 100%;
            padding: 13px 46px;
            border: 1.5px solid var(--suave);
            border-radius: 999px;
            background: #fff;
            color: var(--texto);
            font: inherit;
            font-size: 13.5px;
            transition: border-color .15s, box-shadow .15s;
        }

        .campo input::placeholder {
            color: #a99ea4;
        }

        .campo input:focus {
            outline: none;
            border-color: var(--acento);
            box-shadow: 0 0 0 4px rgba(215, 125, 157, .16);
        }

        .campo .ico {
            position: absolute;
            left: 17px;
            top: 50%;
            transform: translateY(-50%);
            display: grid;
            place-items: center;
            color: var(--medio);
            pointer-events: none;
        }

        .campo .ico svg {
            width: 17px;
            height: 17px;
        }

        .ver {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            display: grid;
            place-items: center;
            width: 32px;
            height: 32px;
            border: 0;
            border-radius: 50%;
            background: transparent;
            color: var(--texto-2);
            cursor: pointer;
        }

        .ver:hover {
            background: var(--crema);
        }

        .ver svg {
            width: 17px;
            height: 17px;
        }

        .ver.on {
            color: var(--acento);
        }

        .error {
            margin: 6px 0 0 18px;
            font-size: 12px;
            color: var(--error);
        }

        .opciones {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin: 16px 4px 20px;
            font-size: 12.5px;
        }

        .check {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--texto-2);
            cursor: pointer;
        }

        .check input {
            width: 15px;
            height: 15px;
            accent-color: var(--medio);
            cursor: pointer;
        }

        .opciones a {
            color: var(--medio);
            font-weight: 600;
            text-decoration: none;
        }

        .opciones a:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            border: 0;
            border-radius: 999px;
            background: var(--medio);
            color: #fff;
            font: inherit;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 10px 22px rgba(215, 125, 157, .28);
            transition: background .15s, transform .1s;
        }

        .btn-login:hover {
            background: #C96F90;
        }

        .btn-login:active {
            transform: translateY(1px);
        }

        .btn-login[disabled] {
            opacity: .7;
            cursor: wait;
        }

        .pie {
            margin-top: auto;
            padding-top: 26px;
            text-align: center;
            font-size: 11.5px;
            color: #a99ea4;
        }

        /* PANEL DERECHO: IDENTIDAD DE MARCA */

        .panel-arte {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            text-align: center;
            color: var(--oscuro);
            background: linear-gradient(145deg, var(--crema), var(--durazno), var(--suave));
        }

        .arte-svg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .arte-contenido {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 430px;
        }

        .logo-principal {
            width: 230px;
            height: 230px;
            object-fit: contain;
            border-radius: 50%;
            background: rgba(255, 255, 255, .55);
            padding: 8px;
            box-shadow: 0 15px 35px rgba(89, 59, 75, .12);
            margin-bottom: 22px;
        }

        .marca-titulo {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -.8px;
            color: var(--oscuro);
        }

        .marca-subtitulo {
            margin-top: 7px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 3px;
            color: #9C617A;
        }

        .separador-marca {
            width: 65px;
            height: 3px;
            margin: 25px 0;
            border-radius: 10px;
            background: var(--medio);
        }

        .arte-bottom h2 {
            font-size: clamp(30px, 4vw, 43px);
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--oscuro);
        }

        .arte-bottom p {
            max-width: 370px;
            margin: 13px auto 0;
            font-size: 14px;
            line-height: 1.8;
            color: #725365;
        }

        .frase-final {
            margin-top: 25px;
            font-size: 13px;
            font-weight: 600;
            color: #9C617A;
        }

        /* RESPONSIVE */

        @media (max-width: 900px) {
            body {
                padding: 16px;
            }

            .shell {
                grid-template-columns: 1fr;
                max-width: 440px;
                min-height: 0;
            }

            .panel-arte {
                grid-row: 1;
                padding: 30px 20px;
            }

            .logo-principal {
                width: 115px;
                height: 115px;
                margin-bottom: 12px;
            }

            .marca-titulo {
                font-size: 26px;
            }

            .marca-subtitulo {
                font-size: 10px;
            }

            .separador-marca {
                margin: 15px 0;
            }

            .arte-bottom h2 {
                font-size: 26px;
            }

            .arte-bottom p {
                font-size: 12px;
                margin-top: 8px;
            }

            .frase-final {
                margin-top: 12px;
                font-size: 11px;
            }

            .panel-login {
                padding: 30px 28px 25px;
            }

            .brand {
                display: none;
            }

            .avatar {
                margin-top: 5px;
            }
        }

        @media (max-width: 420px) {
            body {
                padding: 12px;
            }

            .panel-login {
                padding: 25px 20px;
            }

            .opciones {
                font-size: 11px;
            }
        }

        .sr {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0 0 0 0);
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <main class="shell">

        <!-- FORMULARIO DE LOGIN -->
        <section class="panel-login">

            <div class="avatar" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>

            <h1 class="titulo">Iniciar sesión</h1>
            <p class="subtitulo">Ingresa con tu cuenta para continuar</p>

            @if (session('status'))
                <div class="aviso ok">{{ session('status') }}</div>
            @endif

            @if (session('ok'))
                <div class="aviso ok">{{ session('ok') }}</div>
            @endif

            @if (session('error'))
                <div class="aviso error">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" id="formLogin" novalidate>
                @csrf

                <div class="campo">
                    <label for="email" class="sr">Correo electrónico</label>
                    <span class="ico" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="16" x="2" y="4" rx="2"/>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           placeholder="Correo electrónico" autocomplete="username" required autofocus>
                </div>

                @error('email')
                    <p class="error">{{ $message }}</p>
                @enderror

                <div class="campo">
                    <label for="password" class="sr">Contraseña</label>
                    <span class="ico" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </span>
                    <input id="password" type="password" name="password"
                           placeholder="Contraseña" autocomplete="current-password" required>

                    <button type="button" class="ver" id="verPw" aria-label="Mostrar contraseña">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>

                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror

                <div class="opciones">
                    <label class="check">
                        <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                        <span>Recordarme</span>
                    </label>

                    <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="btn-login" id="btnLogin">
                    Ingresar
                </button>

                <div style="text-align:center; margin-top:18px;">
                    <a href="{{ route('publico.index') }}"
                       style="color:var(--medio); font-size:13px; text-decoration:none; font-weight:600; display:inline-flex; align-items:center; gap:6px;">
                        <span>🌸</span> Ver catálogo público para clientes
                    </a>
                </div>
            </form>

            <p class="pie">
                Bella Flor POS · Acceso solo para personal autorizado
            </p>
        </section>

        <!-- PANEL DERECHO: PRESENTACIÓN BELLA FLOR -->
        <aside class="panel-arte" aria-label="Presentación de Bella Flor">

            <svg class="arte-svg" viewBox="0 0 800 640" preserveAspectRatio="xMidYMid slice">
                <defs>
                    <filter id="blurSuave" x="-40%" y="-40%" width="180%" height="180%">
                        <feGaussianBlur stdDeviation="46"/>
                    </filter>
                </defs>

                <ellipse cx="170" cy="600" rx="260" ry="150"
                         fill="#D77D9D" opacity=".20" filter="url(#blurSuave)"/>

                <ellipse cx="720" cy="80" rx="200" ry="120"
                         fill="#FFFFFF" opacity=".45" filter="url(#blurSuave)"/>

                <circle cx="90" cy="100" r="5" fill="#FFFFFF" opacity=".7"/>
                <circle cx="700" cy="500" r="7" fill="#FFFFFF" opacity=".7"/>
                <circle cx="600" cy="120" r="4" fill="#FFFFFF" opacity=".8"/>
            </svg>

            <div class="arte-contenido">

                <img src="{{ asset('img/logo-bella-flor.png') }}"
                     alt="Logo Bella Flor"
                     class="logo-principal">

                <h2 class="marca-titulo">Bella Flor</h2>

                <p class="marca-subtitulo">
                    MINIMARKET
                </p>

                <div class="separador-marca"></div>

                <div class="arte-bottom">
                    <h2>¡Bienvenidos!</h2>

                    <p>
                        Gestiona tus ventas, compras e inventario
                        del minimarket en un solo lugar,
                        de forma rápida y ordenada.
                    </p>
                </div>

                <p class="frase-final">
                    🌸 Calidad, frescura y confianza 🌸
                </p>

            </div>
        </aside>

    </main>

    <script>
        (() => {
            const pw = document.getElementById('password');
            const ver = document.getElementById('verPw');
            const form = document.getElementById('formLogin');
            const btn = document.getElementById('btnLogin');

            ver.addEventListener('click', () => {
                const oculto = pw.type === 'password';
                pw.type = oculto ? 'text' : 'password';
                ver.classList.toggle('on', oculto);
                ver.setAttribute(
                    'aria-label',
                    oculto ? 'Ocultar contraseña' : 'Mostrar contraseña'
                );
            });

            // Evita enviar dos veces el formulario
            form.addEventListener('submit', () => {
                btn.disabled = true;
                btn.textContent = 'Ingresando…';
            });
        })();
    </script>
</body>
</html>
