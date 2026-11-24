<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bella Flor')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --rosa-200: #ffc6dc;
            --rosa-500: #ff5e8c;
            --rosa-600: #e0356c;
            --rosa-700: #b82554;
            --tinta: #3b1230;
            --tinta-suave: #7a4e62;
            --cristal: rgba(255, 255, 255, .62);
            --borde-cristal: rgba(255, 255, 255, .88);
        }
        html, body { min-height: 100%; }
        body {
            font-family: 'Plus Jakarta Sans', "Segoe UI", system-ui, sans-serif;
            color: var(--tinta);
            background:
                radial-gradient(circle at 15% 10%, #ffcce0 0%, transparent 42%),
                radial-gradient(circle at 90% 95%, #ffb3cf 0%, transparent 48%),
                radial-gradient(circle at 50% 50%, #fff0f5 0%, transparent 70%),
                linear-gradient(135deg, #fff4f8 0%, #ffe4ef 55%, #ffd3e6 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 24px; position: relative; overflow-x: hidden;
        }

        /* ---------- Formas decorativas ---------- */
        .forma { position: fixed; pointer-events: none; z-index: 0; }
        .anillo {
            width: 180px; height: 180px; top: 7%; left: 20%;
            border-radius: 50%;
            border: 32px solid #ff85b0;
            border-right-color: transparent;
            transform: rotate(35deg);
            filter: drop-shadow(0 18px 20px rgba(224, 53, 108, .22));
        }
        .pastilla {
            width: 150px; height: 56px; border-radius: 40px;
            background: linear-gradient(160deg, #fff, #ffb3d1 70%, #ff85b0);
            box-shadow: 0 16px 26px rgba(224, 53, 108, .2), inset 0 -6px 10px rgba(224, 53, 108, .15);
        }
        .p1 { top: 33%; left: 14%; transform: rotate(-32deg); }
        .p2 { top: 47%; left: 11%; width: 96px; height: 38px; transform: rotate(-32deg); opacity: .9; }
        .media-luna {
            width: 210px; height: 105px; bottom: 6%; left: 20%;
            border-radius: 210px 210px 0 0;
            background: radial-gradient(circle at 30% 100%, #ffd4e8, #ff80ae 85%);
            box-shadow: 0 22px 34px rgba(224, 53, 108, .25);
        }
        .nube {
            width: 440px; height: 190px; bottom: -40px; right: -60px;
            border-radius: 60% 40% 55% 45% / 60% 55% 45% 40%;
            background: linear-gradient(160deg, #fff 0%, #ffbdd8 55%, #ff90bc 100%);
            box-shadow: 0 24px 40px rgba(224, 53, 108, .18), inset 0 -14px 24px rgba(224, 53, 108, .15);
            transform: rotate(-6deg);
        }
        .onda {
            width: 130px; height: 44px; top: 38%; right: 16%;
            border-radius: 30px;
            background: linear-gradient(160deg, #fff, #ffaacb);
            box-shadow: 0 12px 20px rgba(224, 53, 108, .18);
            transform: rotate(14deg);
        }
        .onda.o2 { top: 45%; right: 12%; width: 90px; height: 34px; transform: rotate(-8deg); opacity: .9; }
        .arco {
            width: 260px; height: 260px; top: -90px; right: 10%;
            border-radius: 50%;
            border: 34px solid rgba(255, 133, 176, .4);
        }

        /* ---------- Tarjeta de cristal kawaii ---------- */
        .card {
            position: relative; z-index: 1;
            width: 100%; max-width: 440px;
            padding: 40px 36px 32px;
            background: var(--cristal);
            -webkit-backdrop-filter: blur(26px) saturate(1.5);
            backdrop-filter: blur(26px) saturate(1.5);
            border: 1.5px solid var(--borde-cristal);
            border-radius: 32px;
            box-shadow: 0 32px 72px rgba(184, 37, 84, .2), inset 0 1px 0 rgba(255, 255, 255, .95);
        }
        .card-wide { max-width: 580px; }

        .marca { text-align: center; margin-bottom: 26px; }
        .marca-icono {
            width: 58px; height: 58px; margin: 0 auto 10px;
            display: grid; place-items: center; font-size: 30px;
            background: linear-gradient(135deg, #fff, #ffd6e8);
            border-radius: 20px;
            box-shadow: 0 10px 24px rgba(224, 53, 108, .22);
        }
        .marca h1 { font-size: 26px; font-weight: 800; color: var(--rosa-600); letter-spacing: -.4px; }
        .marca p { font-size: 14px; color: var(--tinta-suave); margin-top: 2px; font-weight: 500; }

        h2 { font-size: 22px; font-weight: 800; margin-bottom: 6px; }
        .sub { font-size: 14px; color: var(--tinta-suave); line-height: 1.55; margin-bottom: 24px; }

        .campo { margin-bottom: 16px; }
        .campo label { display: block; font-size: 13px; font-weight: 700; margin-bottom: 7px; color: var(--tinta); }
        .entrada { position: relative; }
        .entrada input {
            width: 100%; padding: 13px 16px; font: inherit; font-size: 14.5px; color: var(--tinta);
            background: rgba(255, 255, 255, .88);
            border: 1.5px solid rgba(255, 180, 210, .6); border-radius: 14px; outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .entrada input::placeholder { color: #c4a0b2; }
        .entrada input:focus {
            border-color: var(--rosa-500);
            box-shadow: 0 0 0 4px rgba(255, 94, 140, .2);
            background: #fff;
        }
        .entrada.con-ojo input { padding-right: 48px; }
        .ojo {
            position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
            width: 36px; height: 36px; border: 0; background: none; cursor: pointer;
            color: var(--tinta-suave); border-radius: 10px; display: grid; place-items: center;
            transition: color .15s;
        }
        .ojo:hover { color: var(--rosa-600); }
        .ojo svg { width: 20px; height: 20px; }

        .fila { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin: 4px 0 22px; font-size: 13.5px; flex-wrap: wrap; }
        .recordar { display: flex; align-items: center; gap: 8px; color: var(--tinta-suave); cursor: pointer; }
        .recordar input { width: 17px; height: 17px; accent-color: var(--rosa-500); }
        a.enlace { color: var(--rosa-600); font-weight: 700; text-decoration: none; }
        a.enlace:hover { text-decoration: underline; }

        .btn {
            display: block; width: 100%; padding: 14px; border: 0; cursor: pointer;
            font: inherit; font-size: 15px; font-weight: 700; color: #fff; text-align: center; text-decoration: none;
            background: linear-gradient(135deg, var(--rosa-500), var(--rosa-700));
            border-radius: 14px;
            box-shadow: 0 12px 28px rgba(184, 37, 84, .35);
            transition: transform .15s, box-shadow .15s, filter .15s;
            letter-spacing: .2px;
        }
        .btn:hover { transform: translateY(-2px); filter: brightness(1.07); box-shadow: 0 18px 34px rgba(184, 37, 84, .42); }
        .btn-suave {
            background: rgba(255, 255, 255, .78); color: var(--rosa-700); box-shadow: none;
            border: 1.5px solid var(--rosa-200);
        }
        .btn-suave:hover { box-shadow: none; background: #fff; transform: none; }
        form + form { margin-top: 10px; }

        .aviso { padding: 12px 16px; border-radius: 14px; font-size: 13.5px; line-height: 1.45; margin-bottom: 18px; }
        .aviso-ok { background: rgba(220, 252, 231, .85); color: #166534; border: 1px solid #bbf7d0; }
        .aviso-error { background: rgba(255, 228, 232, .9); color: #9f1239; border: 1px solid #fecdd3; }
        .nota { color: var(--tinta-suave); font-size: 12.5px; margin-top: 6px; }

        .pie { text-align: center; font-size: 13px; color: var(--tinta-suave); margin-top: 22px; }
        .volver { text-align: center; margin-top: 18px; font-size: 14px; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0 14px; }
        .separador { height: 1px; background: rgba(184, 37, 84, .12); margin: 26px 0 22px; }
        h3 { font-size: 17px; font-weight: 700; margin-bottom: 14px; }

        @media (max-width: 899px) { .anillo, .p1, .p2, .media-luna, .onda, .arco { display: none; } }
        @media (max-width: 520px) {
            .card { padding: 30px 22px 26px; border-radius: 26px; }
            .grid-2 { grid-template-columns: 1fr; }
        }
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }
    </style>
</head>
<body>
    <span class="forma arco"></span>
    <span class="forma anillo"></span>
    <span class="forma pastilla p1"></span>
    <span class="forma pastilla p2"></span>
    <span class="forma media-luna"></span>
    <span class="forma onda"></span>
    <span class="forma onda o2"></span>
    <span class="forma nube"></span>

    <main class="card @yield('card_class')">
        <div class="marca">
            <div class="marca-icono">🌸</div>
            <h1>Bella Flor</h1>
            <p>Sistema de Punto de Venta</p>
        </div>

        @yield('content')
    </main>

    <script>
        function verClave(idInput, btn) {
            const input = document.getElementById(idInput);
            const mostrar = input.type === 'password';
            input.type = mostrar ? 'text' : 'password';
            btn.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');
            btn.style.color = mostrar ? 'var(--rosa-600)' : '';
        }
    </script>
</body>
</html>
