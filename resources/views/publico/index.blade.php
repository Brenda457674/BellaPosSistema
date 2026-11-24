<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌸 Bella Flor | {{ __('messages.product_catalog') ?? 'Catálogo Virtual' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            /* ===== PALETA KAWAII-CHIC ELEGANTE (Bella Flor) ===== */
            --rosa-principal: #D77D9D;
            --rosa-hover: #C56889;
            --rosa-oscuro: #A94F73;
            --rosa-suave: #FCEEF2;
            --rosa-borde: #F2D5DF;
            --rosa-acento: #E9A8BD;
            --ciruela: #593B4B;
            --ciruela-claro: #765466;
            --fondo: #FFF8F5;
            --blanco: #ffffff;
            --texto: #493640;
            --texto-2: #806B77;
            --texto-3: #AA96A0;
            --borde: #F0E3E8;
            --verde: #2f855a;
            --verde-bg: #e8f5ee;
            --verde-borde: #c4ebd4;
            --ambar: #b7791f;
            --ambar-bg: #fef6e4;
            --sombra-suave: 0 6px 20px rgba(169, 79, 115, .06);
            --sombra-card: 0 10px 28px rgba(169, 79, 115, .07);
            --sombra-hover: 0 18px 40px rgba(169, 79, 115, .13);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Plus Jakarta Sans', Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: var(--texto);
            background: var(--fondo);
            -webkit-font-smoothing: antialiased;
            line-height: 1.5;
        }

        /* ===== CINTILLO SUPERIOR KAWAII ===== */
        .cintillo-top {
            background: linear-gradient(90deg, #E9A8BD 0%, #D98BA8 55%, #C77A96 100%);
            color: #fff;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .3px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(199, 122, 150, .12);
        }

        .cintillo-top span.destacado {
            background: rgba(255, 255, 255, .25);
            padding: 2px 8px;
            border-radius: 999px;
            font-weight: 700;
        }

        /* ===== BARRA DE NAVEGACIÓN ===== */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, .92);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--borde);
            padding: 12px 24px;
            transition: all .2s ease;
        }

        .navbar-inner {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--texto);
        }

        .brand-logo {
            display: grid;
            place-items: center;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, #E9A8BD, #C56889);
            font-size: 22px;
            box-shadow: 0 6px 16px rgba(169, 79, 115, .18);
            transition: transform .2s ease;
        }

        .brand:hover .brand-logo {
            transform: scale(1.05) rotate(5deg);
        }

        .brand-name {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -.4px;
            background: linear-gradient(135deg, var(--ciruela), var(--rosa-oscuro));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-sub {
            font-size: 11px;
            font-weight: 600;
            color: var(--texto-3);
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-acceso {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 20px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--rosa-principal), var(--rosa-hover));
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .2px;
            transition: all .2s ease;
            box-shadow: 0 6px 18px rgba(169, 79, 115, .22);
            border: 1px solid rgba(255, 255, 255, .3);
        }

        .btn-acceso:hover {
            background: linear-gradient(135deg, var(--rosa-hover), var(--rosa-oscuro));
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(169, 79, 115, .25);
        }

        .select-lang {
            padding: 8px 14px;
            border-radius: 999px;
            border: 1.5px solid var(--borde);
            background: #fff;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--texto-2);
            cursor: pointer;
            outline: none;
            transition: all .15s ease;
        }

        .select-lang:hover, .select-lang:focus {
            border-color: var(--rosa-principal);
            box-shadow: 0 0 0 3px rgba(215, 125, 157, .14);
        }

        /* ===== HERO BANNER ELEGANTE Y KAWAII ===== */
        .hero {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 15% 20%, rgba(255, 255, 255, .3) 0%, transparent 40%),
                radial-gradient(circle at 85% 80%, rgba(233, 168, 189, .42) 0%, transparent 50%),
                linear-gradient(135deg, #E9A8BD 0%, #D77D9D 55%, #B96A89 100%);
            color: #fff;
            padding: 65px 24px 80px;
            text-align: center;
        }

        /* Formas decorativas sutiles en el fondo */
        .hero-sparkle {
            position: absolute;
            pointer-events: none;
            font-size: 28px;
            opacity: .6;
            animation: floatSparkle 4s ease-in-out infinite alternate;
        }
        @keyframes floatSparkle {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(-12px) rotate(15deg); }
        }

        .hero-inner {
            max-width: 860px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 18px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .2);
            backdrop-filter: blur(10px);
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: .4px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, .35);
            box-shadow: 0 4px 15px rgba(0, 0, 0, .08);
        }

        .hero h1 {
            font-size: clamp(32px, 5.5vw, 48px);
            font-weight: 800;
            letter-spacing: -1.2px;
            line-height: 1.18;
            margin-bottom: 16px;
            text-shadow: 0 2px 10px rgba(89, 59, 75, .18);
        }

        .hero p {
            font-size: clamp(14.5px, 2.2vw, 17px);
            color: rgba(255, 255, 255, .95);
            line-height: 1.65;
            margin-bottom: 30px;
            max-width: 720px;
            margin-left: auto;
            margin-right: auto;
        }

        /* ===== FRANJA DE DETALLES Y BENEFICIOS (Kawaii / Calidad) ===== */
        .features-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            max-width: 1100px;
            margin: -36px auto 35px;
            padding: 0 20px;
            position: relative;
            z-index: 10;
        }

        .feature-card {
            background: #fff;
            border-radius: 18px;
            padding: 16px 14px;
            text-align: center;
            box-shadow: var(--sombra-card);
            border: 1px solid var(--borde);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--sombra-hover);
            border-color: var(--rosa-borde);
        }

        .feature-icon {
            font-size: 26px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: var(--rosa-suave);
            border: 1px solid var(--rosa-borde);
        }

        .feature-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--ciruela);
        }

        .feature-desc {
            font-size: 11.5px;
            color: var(--texto-2);
            line-height: 1.35;
        }

        /* ===== BARRA DE BÚSQUEDA Y FILTROS ===== */
        .filtros-wrapper {
            max-width: 1100px;
            margin: 0 auto 35px;
            padding: 0 20px;
        }

        .filtros-card {
            background: #fff;
            border-radius: 20px;
            padding: 18px 22px;
            box-shadow: var(--sombra-suave);
            border: 1px solid var(--borde);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .filtros-top-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .buscador-campo {
            flex: 1 1 280px;
            position: relative;
        }

        .buscador-campo input {
            width: 100%;
            padding: 13px 20px 13px 46px;
            border-radius: 999px;
            border: 1.5px solid var(--borde);
            background: #fff;
            color: var(--texto);
            font: inherit;
            font-size: 13.5px;
            transition: all .18s ease;
        }

        .buscador-campo input:focus {
            outline: none;
            border-color: var(--rosa-principal);
            box-shadow: 0 0 0 4px rgba(215, 125, 157, .14);
        }

        .buscador-campo .ico {
            position: absolute;
            left: 17px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--rosa-principal);
            pointer-events: none;
            display: grid;
            place-items: center;
        }

        .filtro-select {
            flex: 0 1 240px;
        }

        .filtro-select select {
            width: 100%;
            padding: 13px 18px;
            border-radius: 999px;
            border: 1.5px solid var(--borde);
            background: #fff;
            color: var(--texto);
            font: inherit;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all .18s ease;
        }

        .filtro-select select:focus {
            outline: none;
            border-color: var(--rosa-principal);
            box-shadow: 0 0 0 4px rgba(215, 125, 157, .14);
        }

        .btn-buscar {
            padding: 13px 26px;
            border-radius: 999px;
            border: 0;
            background: linear-gradient(135deg, var(--ciruela), var(--ciruela-claro));
            color: #fff;
            font: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s ease;
            box-shadow: 0 4px 12px rgba(59, 18, 48, .2);
        }

        .btn-buscar:hover {
            background: var(--rosa-hover);
            transform: translateY(-1px);
        }

        .btn-limpiar {
            padding: 13px 18px;
            border-radius: 999px;
            border: 1px solid var(--borde);
            background: var(--rosa-suave);
            color: var(--rosa-hover);
            font: inherit;
            font-size: 12.5px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all .15s ease;
        }

        .btn-limpiar:hover {
            background: #F8E1E9;
        }

        /* Chips de categorías rápidas */
        .categorias-chips {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 4px;
            scrollbar-width: thin;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            background: var(--rosa-suave);
            color: var(--ciruela);
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            border: 1px solid var(--rosa-borde);
            transition: all .15s ease;
        }

        .chip:hover {
            background: #F8E1E9;
            transform: translateY(-1px);
        }

        .chip.active {
            background: linear-gradient(135deg, var(--rosa-principal), var(--rosa-hover));
            color: #fff;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(169, 79, 115, .18);
        }

        /* ===== CONTENEDOR Y GRILLA DE PRODUCTOS ===== */
        .contenedor {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 24px 60px;
            flex: 1;
            width: 100%;
        }

        .conteo-resultados {
            margin-bottom: 24px;
            font-size: 14px;
            color: var(--texto-2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 12px;
            border-bottom: 1px dashed var(--borde);
        }

        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(265px, 1fr));
            gap: 24px;
        }

        .producto-card {
            background: #fff;
            border-radius: 22px;
            overflow: hidden;
            border: 1px solid var(--borde);
            box-shadow: var(--sombra-card);
            transition: all .28s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .producto-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--sombra-hover);
            border-color: var(--rosa-acento);
        }

        .producto-imagen-box {
            position: relative;
            height: 195px;
            background: linear-gradient(135deg, #FFF8F5, #FCEEF2);
            display: grid;
            place-items: center;
            overflow: hidden;
            border-bottom: 1px solid #F0E3E8;
        }

        .producto-imagen-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .35s ease;
        }

        .producto-card:hover .producto-imagen-box img {
            transform: scale(1.07);
        }

        .producto-imagen-placeholder {
            font-size: 58px;
            filter: drop-shadow(0 4px 10px rgba(169, 79, 115, .12));
        }

        .categoria-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(255, 255, 255, .9);
            backdrop-filter: blur(8px);
            color: var(--ciruela);
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .3px;
            border: 1px solid rgba(255, 255, 255, .8);
            box-shadow: 0 4px 10px rgba(0, 0, 0, .06);
        }

        .producto-cuerpo {
            padding: 20px 20px 22px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .producto-marca {
            font-size: 11.5px;
            color: var(--rosa-hover);
            text-transform: uppercase;
            letter-spacing: .8px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .producto-nombre {
            font-size: 16px;
            font-weight: 700;
            color: var(--ciruela);
            margin-bottom: 14px;
            line-height: 1.35;
        }

        .producto-pie {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid #F0E3E8;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 10px;
        }

        .precio-wrap {
            display: flex;
            flex-direction: column;
        }

        .precio-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--texto-3);
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .precio-valor {
            font-size: 22px;
            font-weight: 800;
            color: var(--rosa-hover);
            letter-spacing: -.6px;
        }

        .stock-badge {
            font-size: 11.5px;
            font-weight: 700;
            padding: 4px 11px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .stock-ok {
            background: var(--verde-bg);
            color: var(--verde);
            border: 1px solid var(--verde-borde);
        }

        .stock-bajo {
            background: var(--ambar-bg);
            color: var(--ambar);
            border: 1px solid #ffe69c;
        }

        .stock-agotado {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        /* Botón de pedido / consulta WhatsApp */
        .btn-pedido-whatsapp {
            margin-top: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 14px;
            border-radius: 12px;
            background: var(--rosa-suave);
            color: var(--ciruela);
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid var(--rosa-borde);
            transition: all .2s ease;
        }

        .btn-pedido-whatsapp:hover {
            background: linear-gradient(135deg, var(--rosa-principal), var(--rosa-hover));
            color: #fff;
            border-color: transparent;
            box-shadow: 0 4px 14px rgba(169, 79, 115, .18);
        }

        /* ===== PAGINACIÓN ===== */
        .paginacion-wrap {
            margin-top: 40px;
            display: flex;
            justify-content: center;
        }

        /* ===== SIN PRODUCTOS ===== */
        .sin-resultados {
            text-align: center;
            padding: 60px 24px;
            background: #fff;
            border-radius: 24px;
            border: 2px dashed var(--rosa-borde);
            max-width: 520px;
            margin: 40px auto;
            box-shadow: var(--sombra-card);
        }

        .sin-resultados-ico {
            font-size: 54px;
            margin-bottom: 14px;
        }

        .sin-resultados h3 {
            font-size: 20px;
            font-weight: 800;
            color: var(--ciruela);
            margin-bottom: 8px;
        }

        .sin-resultados p {
            font-size: 14px;
            color: var(--texto-2);
            line-height: 1.6;
            margin-bottom: 22px;
        }

        /* ===== SECCIÓN INFORMATIVA DE LA TIENDA (Detalles para el profesor) ===== */
        .tienda-info-banner {
            max-width: 1240px;
            margin: 20px auto 50px;
            padding: 0 24px;
        }

        .tienda-info-card {
            background: linear-gradient(135deg, #fff, #FFF8F5);
            border-radius: 24px;
            padding: 30px;
            border: 1px solid var(--rosa-borde);
            box-shadow: var(--sombra-card);
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .info-col {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .info-icon {
            font-size: 26px;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #fff;
            border: 1px solid var(--rosa-borde);
            display: grid;
            place-items: center;
            box-shadow: 0 4px 12px rgba(169, 79, 115, .10);
            flex-shrink: 0;
        }

        .info-col strong {
            font-size: 14px;
            color: var(--ciruela);
            display: block;
            margin-bottom: 4px;
        }

        .info-col p {
            font-size: 12.5px;
            color: var(--texto-2);
            line-height: 1.5;
        }

        .medios-pago-badges {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .pago-badge {
            background: #fff;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            color: var(--ciruela);
            border: 1px solid var(--borde);
        }

        /* ===== FOOTER KAWAII ===== */
        footer {
            background: linear-gradient(180deg, #FFFFFF, #FFF3F6);
            border-top: 1px solid var(--borde);
            padding: 34px 24px 28px;
            margin-top: auto;
        }

        .footer-inner {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 13px;
            color: var(--texto-2);
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            color: var(--ciruela);
            font-size: 15px;
        }

        .footer-links a {
            color: var(--rosa-hover);
            text-decoration: none;
            font-weight: 700;
            transition: color .15s ease;
        }

        .footer-links a:hover {
            color: var(--ciruela);
            text-decoration: underline;
        }

        /* ===== BOTÓN FLOTANTE WHATSAPP ===== */
        .btn-flotante-wsp {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 100;
            background: #25d366;
            color: #fff;
            padding: 12px 20px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 13.5px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 9px;
            box-shadow: 0 8px 24px rgba(37, 211, 102, .4);
            transition: all .25s ease;
            border: 2px solid #fff;
        }

        .btn-flotante-wsp:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 12px 30px rgba(37, 211, 102, .55);
            color: #fff;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 900px) {
            .features-strip { grid-template-columns: repeat(2, 1fr); }
            .tienda-info-card { grid-template-columns: 1fr; gap: 18px; }
        }

        @media (max-width: 680px) {
            .cintillo-top { font-size: 11px; }
            .navbar { padding: 10px 16px; }
            .hero { padding: 45px 16px 55px; }
            .features-strip { grid-template-columns: 1fr; margin-top: -20px; }
            .filtros-card { padding: 14px; }
            .productos-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 14px; }
            .producto-imagen-box { height: 155px; }
            .footer-inner { flex-direction: column; text-align: center; }
            .btn-flotante-wsp span { display: none; }
            .btn-flotante-wsp { padding: 14px; border-radius: 50%; }
        }
    </style>
</head>

<body>

    <!-- CINTILLO SUPERIOR KAWAII DE BIENVENIDA -->
    <div class="cintillo-top">
        <span>🌸 ¡Bienvenidos a Bella Flor Minimarket & Boutique! ✨</span>
        <span class="destacado">🛍️ Productos 100% Frescos & Seleccionados</span>
        <span>💳 Aceptamos Yape, Plin y Tarjetas</span>
    </div>

    <!-- BARRA DE NAVEGACIÓN -->
    <header class="navbar">
        <div class="navbar-inner">
            <a href="{{ route('publico.index') }}" class="brand">
                <div class="brand-logo">🌸</div>
                <div>
                    <div class="brand-name">Bella Flor</div>
                    <div class="brand-sub">Minimarket & Punto de Venta</div>
                </div>
            </a>

            <div class="nav-actions">
                {{-- Selector de Idioma en Vista Pública --}}
                <form action="{{ route('language.switch') }}" method="POST">
                    @csrf
                    <select
                        name="locale"
                        class="select-lang"
                        onchange="this.form.submit()"
                        aria-label="Seleccionar idioma"
                    >
                        <option value="es" {{ app()->getLocale() === 'es' ? 'selected' : '' }}>🇪🇸 Español</option>
                        <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>🇺🇸 English</option>
                        <option value="it" {{ app()->getLocale() === 'it' ? 'selected' : '' }}>🇮🇹 Italiano</option>
                    </select>
                </form>

                @auth
                    <a href="{{ route('dashboard') }}" class="btn-acceso">
                        <span>🌸 {{ __('messages.go_to_panel') ?? 'Mi Panel' }}</span>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-acceso">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                        <span>{{ __('messages.admin_pos_access') ?? 'Acceso al Sistema' }}</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- HERO BANNER BONITO, KAWAII Y ELEGANTE -->
    <section class="hero">
        <span class="hero-sparkle" style="top:15%; left:8%;">🌸</span>
        <span class="hero-sparkle" style="bottom:20%; right:10%;">✨</span>
        <span class="hero-sparkle" style="top:25%; right:14%;">💖</span>

        <div class="hero-inner">
            <div class="hero-pill">
                <span>🌸</span> {{ __('messages.official_catalog_live') }}
            </div>
            <h1>{{ __('messages.welcome_public_title') }}</h1>
            <p>{{ __('messages.welcome_public_sub') }}</p>
        </div>
    </section>

    <!-- FRANJA DE BENEFICIOS Y DETALLES -->
    <section class="features-strip">
        <div class="feature-card">
            <div class="feature-icon">🌸</div>
            <div class="feature-title">Calidad Seleccionada</div>
            <div class="feature-desc">Productos frescos, garantizados y de las mejores marcas.</div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">⚡</div>
            <div class="feature-title">Atención Rápida</div>
            <div class="feature-desc">Atención amable en mostrador y preparación al instante.</div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">💳</div>
            <div class="feature-title">Todos los Medios de Pago</div>
            <div class="feature-desc">Aceptamos Yape, Plin, Tarjetas Visa/Mastercard y Efectivo.</div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">🛍️</div>
            <div class="feature-title">Precios Justos</div>
            <div class="feature-desc">Las mejores promociones y precios del día para tu hogar.</div>
        </div>
    </section>

    <!-- FORMULARIO DE FILTROS Y BÚSQUEDA -->
    <div class="filtros-wrapper">
        <div class="filtros-card">
            <form action="{{ route('publico.index') }}" method="GET" class="filtros-top-row">

                <div class="buscador-campo">
                    <span class="ico">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="buscar"
                        placeholder="🔍 ¿Qué buscas hoy? (Ej. rosas, leche, galletas, chocolates...)"
                        value="{{ request('buscar') }}"
                    >
                </div>

                <div class="filtro-select">
                    <select name="categoria" onchange="this.form.submit()">
                        <option value="">🌸 {{ __('messages.all_categories') ?? 'Todas las categorías' }}</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn-buscar">
                    {{ __('messages.search') ?? 'Buscar' }}
                </button>

                @if(request('buscar') || request('categoria'))
                    <a href="{{ route('publico.index') }}" class="btn-limpiar">
                        ✕ {{ __('messages.clear') ?? 'Limpiar filtros' }}
                    </a>
                @endif
            </form>

            {{-- Píldoras rápidas de Categorías para clic directo --}}
            <div class="categorias-chips">
                <a href="{{ route('publico.index') }}" class="chip {{ !request('categoria') ? 'active' : '' }}">
                    🌸 Todas
                </a>
                @foreach($categorias as $cat)
                    <a
                        href="{{ route('publico.index', array_merge(request()->except('categoria', 'page'), ['categoria' => $cat->id])) }}"
                        class="chip {{ request('categoria') == $cat->id ? 'active' : '' }}"
                    >
                        {{ $cat->nombre }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- LISTADO DE PRODUCTOS -->
    <main class="contenedor">

        <div class="conteo-resultados">
            <span>
                {{ __('messages.showing') }} <strong>{{ $productos->total() }}</strong> {{ $productos->total() === 1 ? __('messages.product_count') : __('messages.products_count') }} disponibles
            </span>
            @if(request('buscar') || request('categoria'))
                <span style="font-size: 12.5px; font-weight: 700; color: var(--rosa-hover);">
                    Filtros activos 🌸
                </span>
            @endif
        </div>

        @if($productos->count())
            <div class="productos-grid">
                @foreach($productos as $producto)
                    <article class="producto-card">

                        <div class="producto-imagen-box">
                            @if($producto->foto)
                                <img
                                    src="{{ asset('storage/'.$producto->foto) }}"
                                    alt="{{ $producto->nombre }}"
                                    loading="lazy"
                                >
                            @else
                                <div class="producto-imagen-placeholder">🌸</div>
                            @endif

                            @if($producto->categoria)
                                <span class="categoria-badge">{{ $producto->categoria->nombre }}</span>
                            @endif
                        </div>

                        <div class="producto-cuerpo">
                            @if($producto->marca)
                                <div class="producto-marca">{{ $producto->marca }}</div>
                            @else
                                <div class="producto-marca">BELLA FLOR</div>
                            @endif

                            <h3 class="producto-nombre">{{ $producto->nombre }}</h3>

                            <div class="producto-pie">
                                <div class="precio-wrap">
                                    <span class="precio-label">{{ __('messages.price') }}</span>
                                    <span class="precio-valor">S/ {{ number_format($producto->precio_venta, 2) }}</span>
                                </div>

                                <div>
                                    @if($producto->stock_actual > 5)
                                        <span class="stock-badge stock-ok">● {{ __('messages.in_stock') }}</span>
                                    @elseif($producto->stock_actual > 0)
                                        <span class="stock-badge stock-bajo">▲ {{ __('messages.low_stock_badge') }}</span>
                                    @else
                                        <span class="stock-badge stock-agotado">✕ {{ __('messages.out_of_stock') }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Botón rápido para consultar por WhatsApp --}}
                            @php
                                $mensajeWsp = rawurlencode("¡Hola Bella Flor! Quisiera consultar por el producto: {$producto->nombre} (Precio: S/ " . number_format($producto->precio_venta, 2) . ")");
                            @endphp
                            <a
                                href="https://wa.me/51999999999?text={{ $mensajeWsp }}"
                                target="_blank"
                                rel="noopener"
                                class="btn-pedido-whatsapp"
                                title="Preguntar por este producto"
                            >
                                💬 Consultar disponibilidad
                            </a>
                        </div>

                    </article>
                @endforeach
            </div>

            <div class="paginacion-wrap">
                {{ $productos->links() }}
            </div>
        @else
            <div class="sin-resultados">
                <div class="sin-resultados-ico">🌸</div>
                <h3>{{ __('messages.no_products_found_public') }}</h3>
                <p>{{ __('messages.no_products_found_public_sub') }}</p>
                <a href="{{ route('publico.index') }}" class="btn-acceso" style="display:inline-flex;">
                    Ver todos los productos 🌸
                </a>
            </div>
        @endif

    </main>

    <!-- BANNER DE DETALLES DE LA TIENDA (HORARIOS, UBICACIÓN, PAGOS) -->
    <section class="tienda-info-banner">
        <div class="tienda-info-card">
            <div class="info-col">
                <div class="info-icon">🕒</div>
                <div>
                    <strong>Horario de Atención</strong>
                    <p>Lunes a Sábado: 8:00 AM - 9:00 PM<br>Domingos: 9:00 AM - 6:00 PM</p>
                </div>
            </div>

            <div class="info-col">
                <div class="info-icon">📍</div>
                <div>
                    <strong>Ubicación & Envíos</strong>
                    <p>Atención directa en tienda física.<br>Envíos y reservas por WhatsApp.</p>
                </div>
            </div>

            <div class="info-col">
                <div class="info-icon">💳</div>
                <div>
                    <strong>Medios de Pago</strong>
                    <p>Aceptamos pagos rápidos y seguros:</p>
                    <div class="medios-pago-badges">
                        <span class="pago-badge">📱 Yape</span>
                        <span class="pago-badge">🟣 Plin</span>
                        <span class="pago-badge">💳 Visa</span>
                        <span class="pago-badge">💳 Mastercard</span>
                        <span class="pago-badge">💵 Efectivo</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER ELEGANTE -->
    <footer>
        <div class="footer-inner">
            <div class="footer-brand">
                <span>🌸</span>
                <span>Bella Flor Minimarket & POS</span>
            </div>
            <div>
                © {{ date('Y') }} Bella Flor. {{ __('messages.all_rights_reserved') }} • Calidad y Cercanía Siempre ✨
            </div>
            <div class="footer-links">
                <a href="{{ route('login') }}">{{ __('messages.admin_pos_access') }} →</a>
            </div>
        </div>
    </footer>

    <!-- BOTÓN FLOTANTE WHATSAPP -->
    <a
        href="https://wa.me/51999999999?text=Hola%20Bella%20Flor,%20tengo%20una%20consulta"
        target="_blank"
        rel="noopener"
        class="btn-flotante-wsp"
        title="Escríbenos por WhatsApp"
    >
        <span>💬 ¿Consultas? ¡Escríbenos!</span>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-5.46-4.45-9.91-9.91-9.91zm0 18.15c-1.52 0-3.01-.41-4.31-1.18l-.31-.18-3.12.82.83-3.04-.2-.32c-.84-1.34-1.28-2.9-1.28-4.52 0-4.54 3.7-8.24 8.24-8.24 4.54 0 8.24 3.7 8.24 8.24 0 4.54-3.7 8.24-8.24 8.24zm4.52-6.16c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.23.25-.87.85-.87 2.08 0 1.23.89 2.42 1.02 2.59.12.17 1.76 2.69 4.26 3.77.6.26 1.06.41 1.42.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.12-.22-.19-.47-.31z"/>
        </svg>
    </a>

</body>
</html>
