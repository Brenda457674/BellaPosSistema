<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Panel') | Bella Flor POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/js/app.js'])

    <style>
        @keyframes slideInToast {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        :root {
            /* ===== PALETA KAWAII-CHIC BELLA FLOR ===== */
            --rosa: #ff5e8c;
            --rosa-oscuro: #e03268;
            --rosa-suave: #fff0f5;
            --rosa-borde: #ffd3e2;
            --ciruela: #3b1230;
            --ciruela-2: #24081e;
            --crema: #fff6f8;
            --durazno: #ffd8e4;
            --fondo: #fcf7fa;
            --blanco: #ffffff;
            --texto: #2b1824;
            --texto-2: #6b5361;
            --texto-3: #9e8494;
            --borde: #f0e1e8;
            --ambar: #b7791f;
            --ambar-bg: #fef6e4;
            --verde: #2f855a;
            --verde-bg: #e8f5ee;
            --azul: #3b6fd4;
            --azul-bg: #e9f0fd;
            --sombra: 0 4px 20px rgba(224, 53, 108, .06), 0 1px 3px rgba(0, 0, 0, .03);
            --ancho-menu: 258px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', Inter, "Segoe UI", system-ui, -apple-system, Arial, sans-serif;
            font-size: 14px;
            color: var(--texto);
            background: var(--fondo);
            -webkit-font-smoothing: antialiased;
        }

        button, a { font: inherit; }
        svg.i { width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }

        /* ---------- Barra lateral (ciruela) ---------- */
        .sidebar {
            position: fixed; inset: 0 auto 0 0;
            width: var(--ancho-menu);
            display: flex; flex-direction: column;
            background:
                radial-gradient(280px 240px at 0% 0%, rgba(255, 94, 140, .32), transparent 75%),
                linear-gradient(180deg, #38122d 0%, #1f071a 100%);
            color: var(--crema);
            z-index: 100;
            transition: transform .25s ease;
            box-shadow: 4px 0 24px rgba(42, 12, 34, .08);
        }

        .brand { display: flex; align-items: center; gap: 12px; padding: 22px 22px 18px; }
        .brand-logo {
            display: grid; place-items: center; width: 42px; height: 42px;
            border-radius: 13px; background: linear-gradient(135deg, #ff80a3, #e03268);
            font-size: 22px; box-shadow: 0 6px 16px rgba(224, 50, 104, .45);
        }
        .brand-name { font-size: 16px; font-weight: 700; letter-spacing: -.2px; color: #fff; }
        .brand-sub { margin-top: 2px; font-size: 11px; color: rgba(255, 241, 228, .65); }

        .nav { flex: 1; overflow-y: auto; padding: 6px 14px 14px; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,.18) transparent; }
        .nav-label {
            padding: 16px 10px 8px; font-size: 10.5px; font-weight: 700;
            letter-spacing: 1.2px; text-transform: uppercase; color: rgba(255, 241, 228, .45);
        }
        .nav-item {
            position: relative; display: flex; align-items: center; gap: 12px;
            padding: 10px 14px; margin-bottom: 3px; border-radius: 12px;
            color: rgba(255, 241, 228, .82); text-decoration: none; font-weight: 500;
            transition: background .15s, color .15s, transform .15s;
        }
        a.nav-item:hover { background: rgba(255, 255, 255, .1); color: #fff; transform: translateX(2px); }
        .nav-item.active {
            background: linear-gradient(135deg, #ff5e8c, #d82b60);
            color: #fff; font-weight: 600;
            box-shadow: 0 8px 20px rgba(216, 43, 96, .38);
        }
        .nav-item.soon { color: rgba(255, 241, 228, .35); cursor: default; }
        .soon-tag {
            margin-left: auto; padding: 2px 7px; border-radius: 20px;
            background: rgba(255, 255, 255, .1); color: rgba(255, 241, 228, .5);
            font-size: 9.5px; font-weight: 700; letter-spacing: .4px;
        }

        .sidebar-foot { padding: 14px; border-top: 1px solid rgba(255, 255, 255, .08); }
        .foot-user { display: flex; align-items: center; gap: 10px; padding: 8px; border-radius: 12px; background: rgba(255, 255, 255, .06); }
        .foot-user .avatar { width: 36px; height: 36px; font-size: 12px; background: rgba(255, 255, 255, .14); color: #fff; border-color: rgba(255, 255, 255, .2); }
        .foot-name { font-size: 13px; font-weight: 600; line-height: 1.2; color: #fff; }
        .foot-role { font-size: 11px; color: rgba(255, 241, 228, .55); margin-top: 2px; }

        /* ---------- Contenido ---------- */
        .main { margin-left: var(--ancho-menu); min-height: 100vh; display: flex; flex-direction: column; }

        .topbar {
            position: sticky; top: 0; z-index: 50;
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
            height: 66px; padding: 0 32px;
            background: rgba(255, 255, 255, .92);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--borde);
        }
        .topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .menu-btn {
            display: none; place-items: center; width: 38px; height: 38px;
            border: 1px solid var(--borde); border-radius: 10px; background: white;
            color: var(--texto); cursor: pointer;
        }
        .crumbs { font-size: 13px; color: var(--texto-3); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .crumbs strong { color: var(--rosa); font-weight: 700; }
        .topbar-right { display: flex; align-items: center; gap: 14px; }
        .fecha { font-size: 12.5px; color: var(--texto-2); text-transform: capitalize; }

        .user-menu { position: relative; }
        .user-btn {
            display: flex; align-items: center; gap: 10px; padding: 5px 10px 5px 5px;
            border: 1px solid var(--borde); border-radius: 999px; background: white;
            color: var(--texto); cursor: pointer; text-align: left;
            box-shadow: var(--sombra); transition: all .15s ease;
        }
        .user-btn:hover { background: var(--rosa-suave); border-color: var(--rosa-borde); }
        .avatar {
            display: grid; place-items: center; width: 34px; height: 34px; border-radius: 50%;
            background: linear-gradient(135deg, #ff80a3, #e03268); color: #fff;
            font-size: 12px; font-weight: 800;
        }
        .user-name { font-size: 13px; font-weight: 700; line-height: 1.2; }
        .user-role { font-size: 11px; color: var(--texto-3); margin-top: 1px; }
        .user-btn .i { width: 15px; height: 15px; color: var(--texto-3); }

        .dropdown {
            display: none; position: absolute; right: 0; top: calc(100% + 8px);
            min-width: 215px; padding: 6px; background: white;
            border: 1px solid var(--borde); border-radius: 18px;
            box-shadow: 0 20px 50px rgba(59, 18, 48, .18);
        }
        .dropdown.open { display: block; }
        .dd-item {
            display: flex; align-items: center; gap: 10px; width: 100%;
            padding: 10px 12px; border: 0; border-radius: 10px; background: transparent;
            color: var(--texto); font-size: 13px; text-decoration: none; text-align: left; cursor: pointer;
            transition: background .12s;
        }
        .dd-item:hover { background: var(--fondo); }
        .dd-item.danger { color: #c0344f; }
        .dd-item.danger:hover { background: #fff0f2; }
        .dd-sep { height: 1px; margin: 6px 0; background: var(--borde); }

        .content { flex: 1; padding: 30px 32px; max-width: 1400px; width: 100%; }

        .page-head { margin-bottom: 26px; }
        .page-head h1 { font-size: 25px; font-weight: 800; letter-spacing: -.5px; color: var(--ciruela); }
        .page-head p { margin-top: 6px; color: var(--texto-2); }

        /* ---------- KPIs ---------- */
        .kpis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 24px; }
        .kpis.tres { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .kpi {
            padding: 20px; background: var(--blanco);
            border: 1px solid var(--borde); border-radius: 20px; box-shadow: var(--sombra);
            transition: transform .2s, box-shadow .2s;
        }
        .kpi:hover { transform: translateY(-4px); box-shadow: 0 16px 32px rgba(224, 50, 104, .12); border-color: var(--rosa-borde); }
        .kpi-top { display: flex; align-items: center; justify-content: space-between; }
        .kpi-label { font-size: 13px; font-weight: 600; color: var(--texto-2); }
        .kpi-icon { display: grid; place-items: center; width: 44px; height: 44px; border-radius: 14px; font-size: 18px; }
        .kpi-icon.rosa  { background: var(--rosa-suave); color: var(--rosa-oscuro); }
        .kpi-icon.azul  { background: var(--azul-bg);   color: var(--azul); }
        .kpi-icon.ambar { background: var(--ambar-bg);  color: var(--ambar); }
        .kpi-icon.verde { background: var(--verde-bg);  color: var(--verde); }
        .kpi-value { margin-top: 14px; font-size: 30px; font-weight: 800; letter-spacing: -.8px; color: var(--ciruela); overflow-wrap: anywhere; }
        .kpi-note { margin-top: 6px; font-size: 12px; color: var(--texto-3); }

        .grid-2 { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 16px; margin-bottom: 24px; }
        .card { background: var(--blanco); border: 1px solid var(--borde); border-radius: 20px; box-shadow: var(--sombra); }
        .card-head {
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            padding: 18px 22px; border-bottom: 1px solid var(--borde);
        }
        .card-head h2 { font-size: 15px; font-weight: 700; color: var(--ciruela); }
        .card-head span { font-size: 12px; color: var(--texto-3); }
        .card-body { padding: 20px; }

        table { width: 100%; border-collapse: collapse; }
        th {
            padding: 12px 22px; text-align: left; font-size: 11px; font-weight: 700;
            letter-spacing: .7px; text-transform: uppercase; color: var(--texto-3);
            background: var(--rosa-suave); border-bottom: 1px solid var(--borde);
        }
        td { padding: 15px 22px; border-bottom: 1px solid var(--borde); }
        tbody tr { transition: background .12s; }
        tbody tr:hover { background: #fff8fb; }

        .empty { display: grid; justify-items: center; gap: 8px; padding: 42px 20px; text-align: center; }
        .empty-icon {
            display: grid; place-items: center; width: 50px; height: 50px; border-radius: 50%;
            background: var(--rosa-suave); color: var(--rosa); font-size: 20px;
        }
        .empty-icon .i { width: 22px; height: 22px; }
        .empty h3 { font-size: 14px; font-weight: 600; }
        .empty p { max-width: 280px; font-size: 12.5px; color: var(--texto-3); line-height: 1.5; }

        .actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 12px; }
        .action {
            display: flex; align-items: center; gap: 12px; padding: 14px 16px;
            background: var(--blanco); border: 1px solid var(--borde); border-radius: 16px;
            color: var(--texto); text-decoration: none; font-weight: 600; font-size: 13px;
            transition: border-color .15s, box-shadow .15s, transform .15s;
        }
        a.action:hover { border-color: var(--rosa-borde); box-shadow: 0 8px 22px rgba(224, 50, 104, .12); transform: translateY(-2px); }
        .action .ic { display: grid; place-items: center; width: 40px; height: 40px; border-radius: 12px; background: var(--rosa-suave); color: var(--rosa-oscuro); }
        .action.soon { color: var(--texto-3); }
        .action.soon .ic { background: var(--fondo); color: var(--texto-3); }
        .action small { display: block; margin-top: 2px; font-weight: 500; font-size: 11px; color: var(--texto-3); }

        .section-title { margin: 6px 0 14px; font-size: 15px; font-weight: 700; color: var(--ciruela); }

        .footer { padding: 18px 32px 26px; font-size: 12px; color: var(--texto-3); }
        .overlay { display: none; }

        /* ---------- Cabecera con acciones ---------- */
        .page-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
        .page-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        /* ---------- Botones ---------- */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 10px 16px; border: 1px solid var(--borde); border-radius: 12px;
            background: white; color: var(--texto); font-size: 13px; font-weight: 600;
            text-decoration: none; cursor: pointer; white-space: nowrap;
            transition: background .15s, border-color .15s;
        }
        .btn:hover { background: var(--fondo); }
        .btn-primary {
            background: linear-gradient(135deg, var(--rosa), var(--rosa-oscuro));
            border-color: transparent;
            color: white;
            box-shadow: 0 6px 16px rgba(224, 50, 104, .28);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--rosa-oscuro), #c41c53);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(224, 50, 104, .38);
        }
        .btn-danger { color: #c0344f; border-color: #fecdd3; background: #fff; }
        .btn-danger:hover { background: #ffe4e6; border-color: #fda4af; }
        .btn-sm { padding: 6px 10px; font-size: 12px; border-radius: 9px; }
        .btn-block { width: 100%; }
        .btn[disabled] { opacity: .55; cursor: not-allowed; }

        /* ---------- Mensajes ---------- */
        .alert { margin-bottom: 20px; padding: 12px 16px; border-radius: 12px; font-size: 13px; line-height: 1.5; }
        .alert ul { margin: 6px 0 0 18px; }
        .alert-ok { background: var(--verde-bg); color: var(--verde); border: 1px solid #c7e6d4; }
        .alert-error { background: #fdecef; color: var(--rosa-oscuro); border: 1px solid var(--rosa-borde); }

        /* ---------- Formularios ---------- */
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .form-grid .full { grid-column: 1 / -1; }
        .field label { display: block; margin-bottom: 6px; font-size: 12.5px; font-weight: 600; color: var(--texto-2); }
        .field input, .field select, .field textarea, .input {
            width: 100%; padding: 10px 12px; border: 1px solid var(--borde); border-radius: 12px;
            background: white; color: var(--texto); font: inherit; font-size: 13.5px;
        }
        .field input:focus, .field select:focus, .field textarea:focus, .input:focus {
            outline: none; border-color: var(--rosa); box-shadow: 0 0 0 3px rgba(214, 90, 122, .15);
        }
        .field small { display: block; margin-top: 5px; color: var(--texto-3); font-size: 11.5px; }
        .form-actions { display: flex; gap: 10px; margin-top: 22px; flex-wrap: wrap; }

        /* ---------- Tablas y filtros ---------- */
        .toolbar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; padding: 16px 20px; border-bottom: 1px solid var(--borde); }
        .toolbar .input { max-width: 300px; }
        .table-wrap { overflow-x: auto; }
        tr:last-child td { border-bottom: 0; }
        td.num, th.num { text-align: right; white-space: nowrap; }
        td.acciones { text-align: right; white-space: nowrap; }
        td.acciones form { display: inline; }
        .muted { color: var(--texto-3); }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .badge-ok { background: var(--verde-bg); color: var(--verde); }
        .badge-warn { background: var(--ambar-bg); color: var(--ambar); }
        .badge-bad { background: #fdecef; color: var(--rosa-oscuro); }
        .badge-gray { background: var(--fondo); color: var(--texto-2); }

        .detalle { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
        .detalle dt { font-size: 11.5px; font-weight: 600; letter-spacing: .4px; text-transform: uppercase; color: var(--texto-3); }
        .detalle dd { margin-top: 4px; font-size: 14px; font-weight: 500; }
        .totales { margin-left: auto; max-width: 320px; padding: 16px 20px; }
        .totales div { display: flex; justify-content: space-between; padding: 5px 0; }
        .totales .total { margin-top: 6px; padding-top: 10px; border-top: 1px solid var(--borde); font-size: 18px; font-weight: 700; }

        /* ---------- Punto de venta ---------- */
        .pos { display: grid; grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr); gap: 16px; align-items: start; }
        .pos-products { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 12px; padding: 16px 20px 20px; max-height: 62vh; overflow-y: auto; }
        .prod-card {
            padding: 12px; border: 1px solid var(--borde); border-radius: 14px; background: white;
            text-align: left; cursor: pointer; font: inherit; color: inherit;
        }
        .prod-card:hover { border-color: var(--rosa); box-shadow: var(--sombra); }
        .prod-card.sin-stock { opacity: .5; cursor: not-allowed; }
        .prod-card strong { display: block; font-size: 13px; line-height: 1.3; }
        .prod-card .precio { margin-top: 6px; color: var(--rosa-oscuro); font-weight: 700; }
        .prod-card .stock { margin-top: 2px; font-size: 11.5px; color: var(--texto-3); }
        .cart-empty { padding: 30px 20px; text-align: center; color: var(--texto-3); font-size: 13px; }
        .cart-line { display: grid; grid-template-columns: 1fr auto; gap: 6px 10px; padding: 12px 20px; border-bottom: 1px solid var(--borde); }
        .cart-line .nombre { font-weight: 600; font-size: 13px; }
        .cart-line .sub { text-align: right; font-weight: 700; }
        .cart-qty { display: flex; align-items: center; gap: 6px; }
        .cart-qty button { width: 28px; height: 28px; border: 1px solid var(--borde); border-radius: 8px; background: white; cursor: pointer; font-size: 15px; }
        .cart-qty input { width: 70px; padding: 5px 6px; border: 1px solid var(--borde); border-radius: 8px; text-align: center; font: inherit; }
        .cart-remove { border: 0; background: transparent; color: var(--rosa-oscuro); cursor: pointer; font-size: 12px; }
        .cart-form { padding: 16px 20px 20px; display: grid; gap: 12px; }
        .cart-total { display: flex; justify-content: space-between; font-size: 22px; font-weight: 800; }

        /* ---------- Barras de reportes ---------- */
        .bar-row { display: grid; grid-template-columns: 110px 1fr 100px; gap: 10px; align-items: center; padding: 6px 20px; font-size: 13px; }
        .bar { height: 10px; border-radius: 6px; background: var(--rosa-suave); overflow: hidden; }
        .bar span { display: block; height: 100%; background: var(--rosa); }

        @media (max-width: 1000px) { .pos { grid-template-columns: 1fr; } }
        @media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } }
        @media print {
            .sidebar, .topbar, .page-actions, .footer, .no-print { display: none !important; }
            .main { margin-left: 0; }
            .content { padding: 0; }
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 1150px) {
            .kpis, .kpis.tres { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .grid-2 { grid-template-columns: 1fr; }
        }
        @media (max-width: 860px) {
            .sidebar { transform: translateX(-100%); box-shadow: none; }
            .sidebar.open { transform: translateX(0); box-shadow: 0 0 40px rgba(0, 0, 0, .3); }
            .overlay.open { display: block; position: fixed; inset: 0; z-index: 90; background: rgba(42, 12, 34, .5); }
            .main { margin-left: 0; }
            .menu-btn { display: grid; }
            .topbar { padding: 0 16px; }
            .content { padding: 20px 16px; }
            .fecha, .user-info { display: none; }
            .user-btn { padding: 4px; }
            .user-btn .i { display: none; }
            .footer { padding: 18px 16px 26px; }
        }
        @media (max-width: 520px) {
            .kpis, .kpis.tres { grid-template-columns: 1fr; }
            .page-head h1 { font-size: 21px; }
        }
    </style>
    @yield('styles')
    @stack('styles')
</head>

<body>
@php
    $usuario = $usuario ?? auth()->user();

    $ic = [
        'home'    => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        'box'     => '<path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
        'card'    => '<rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/>',
        'cart'    => '<circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>',
        'list'    => '<rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/>',
        'chart'   => '<line x1="12" x2="12" y1="20" y2="10"/><line x1="18" x2="18" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="16"/>',
        'users'   => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'user'    => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'gear'    => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.93 4.93l2.12 2.12M16.95 16.95l2.12 2.12M2 12h3M19 12h3M4.93 19.07l2.12-2.12M16.95 7.05l2.12-2.12"/>',
        'truck'   => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>',
        'alert'   => '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
        'cash'    => '<rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>',
        'receipt' => '<path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 17.5v-11"/>',
        'plus'    => '<path d="M5 12h14"/><path d="M12 5v14"/>',
        'menu'    => '<line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>',
        'down'    => '<path d="m6 9 6 6 6-6"/>',
        'shield'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'logout'  => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>',
    ];
    $icon = fn (string $n) => '<svg class="i" viewBox="0 0 24 24" aria-hidden="true">' . ($ic[$n] ?? '') . '</svg>';

    $rolNombre = $usuario->rol?->nombre ?? '';
    $nombres   = $usuario->nombres ?? $usuario->name ?? 'Usuario';
    $apellidos = $usuario->apellidos ?? '';
    $nombreCompleto = trim($nombres . ' ' . $apellidos);
    $iniciales = strtoupper(mb_substr($nombres, 0, 1) . mb_substr($apellidos ?: $nombres, 0, 1));
    $rolEtiqueta = match ($rolNombre) {
        'Administrador' => 'Administradora',
        default => $rolNombre ?: 'Usuario',
    };

    // Menú según el rol. href = null significa "no disponible".
    $r = fn (string $name) => \Illuminate\Support\Facades\Route::has($name) ? route($name) : null;


$menu = match ($rolNombre) {
    'Administrador' => [
        [__('messages.dashboard'), 'home', url('/admin'), ['admin.dashboard']],
        [__('messages.products'), 'box', $r('admin.productos.index'), ['admin.productos.*']],
        [__('messages.categories'), 'list', $r('categorias.index'), ['categorias.*', 'unidades.*']],
        [__('messages.new_sale'), 'card', $r('pos.create'), ['pos.create']],
        [__('messages.sales'), 'receipt', $r('pos.index'), ['pos.index', 'pos.show']],
        [__('messages.purchases'), 'cart', $r('compras.index'), ['compras.*']],
        [__('messages.inventory'), 'list', $r('inventario.index'), ['inventario.*']],
        [__('messages.suppliers'), 'truck', $r('proveedores.index'), ['proveedores.*']],
        [__('messages.customers'), 'users', $r('clientes.index'), ['clientes.*']],
        [__('messages.reports'), 'chart', $r('reportes.index'), ['reportes.*']],
        [__('messages.users'), 'shield', $r('usuarios.index'), ['usuarios.*']],
        [__('messages.settings'), 'gear', $r('configuracion.edit'), ['configuracion.*']],
    ],

    'Vendedor' => [
        [__('messages.home'), 'home', url('/ventas'), ['ventas.index']],
        [__('messages.products'), 'box', $r('admin.productos.index'), ['admin.productos.*']],
        [__('messages.new_sale'), 'card', $r('pos.create'), ['pos.create']],
        [__('messages.my_sales'), 'receipt', $r('pos.index'), ['pos.index', 'pos.show']],
        [__('messages.customers'), 'users', $r('clientes.index'), ['clientes.*']],
    ],

    'Almacenero' => [
        [__('messages.home'), 'home', url('/almacen'), ['almacen.index']],
        [__('messages.products'), 'box', $r('admin.productos.index'), ['admin.productos.*']],
        [__('messages.categories'), 'list', $r('categorias.index'), ['categorias.*', 'unidades.*']],
        [__('messages.inventory'), 'list', $r('inventario.index'), ['inventario.*']],
        [__('messages.purchases'), 'cart', $r('compras.index'), ['compras.*']],
        [__('messages.suppliers'), 'truck', $r('proveedores.index'), ['proveedores.*']],
    ],

    default => [],
};

    $urlPerfil = url('/perfil');
@endphp

    <div class="overlay" id="overlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-logo">🌸</div>
            <div>
                <div class="brand-name">Bella Flor</div>
                <div class="brand-sub">{{ __('messages.pos_system') }}</div>
            </div>
        </div>

        <nav class="nav" aria-label="Navegación principal">
            <div class="nav-label">{{ __('messages.menu') }}</div>

            @foreach ($menu as [$label, $iconName, $href, $patron])
                @if ($href)
                    <a class="nav-item {{ request()->routeIs(...$patron) ? 'active' : '' }}" href="{{ $href }}">
                        {!! $icon($iconName) !!} {{ $label }}
                    </a>
                @else
                    <span class="nav-item soon">
                        {!! $icon($iconName) !!} {{ $label }}
                        <span class="soon-tag">PRONTO</span>
                    </span>
                @endif
            @endforeach

            <div class="nav-label">{{ __('messages.account') }}</div>
            <a class="nav-item {{ request()->url() === $urlPerfil ? 'active' : '' }}" href="{{ $urlPerfil }}">
                {!! $icon('user') !!} {{ __('messages.profile') }}
            </a>
        </nav>

        <div class="sidebar-foot">
            <div class="foot-user">
                <div class="avatar">{{ $iniciales }}</div>
                <div>
                    <div class="foot-name">{{ $nombres }}</div>
                    <div class="foot-role">{{ $rolEtiqueta }}</div>
                </div>
            </div>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="menu-btn" id="menuBtn" aria-label="Abrir menú" aria-expanded="false">
                    {!! $icon('menu') !!}
                </button>
                <div class="crumbs">Bella Flor / <strong>@yield('titulo', 'Panel')</strong></div>
            </div>


<div class="topbar-right">

    {{-- Fecha según el idioma seleccionado --}}
    <span class="fecha">
        {{ now()->locale(app()->getLocale())->translatedFormat('l, d \d\e F \d\e Y') }}
    </span>



{{-- Selector de idioma --}}
<form action="{{ route('language.switch') }}" method="POST">
    @csrf

    <select
        name="locale"
        class="btn"
        aria-label="Seleccionar idioma"
        onchange="this.form.submit()"
    >
        <option value="es" {{ app()->getLocale() === 'es' ? 'selected' : '' }}>
            🇪🇸 Español
        </option>

        <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>
            🇺🇸 English
        </option>

        <option value="it" {{ app()->getLocale() === 'it' ? 'selected' : '' }}>
            🇮🇹 Italiano
        </option>
    </select>
</form>

    {{-- Indicador WebSockets / Notificaciones en tiempo real (Reverb) --}}
    <div class="ws-notif-wrapper" style="position: relative;">
        <button type="button" class="btn" id="wsBellBtn" title="WebSockets (Reverb): Alertas de Stock Bajo y Ventas en Vivo" style="position: relative; display: flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; padding: 0;">
            <svg class="i" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span id="wsBadge" style="display:none; position: absolute; top: -3px; right: -3px; background: #e53e3e; color: #fff; font-size: 10px; font-weight: bold; border-radius: 999px; min-width: 18px; height: 18px; line-height: 18px; text-align: center; padding: 0 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">0</span>
            <span id="wsStatusDot" style="position: absolute; bottom: 2px; right: 2px; width: 9px; height: 9px; background: #38a169; border-radius: 50%; border: 2px solid #fff;" title="Conectado a Reverb (WebSockets)"></span>
        </button>
    </div>

    {{-- Menú del usuario --}}
    <div class="user-menu">
        <button type="button" class="user-btn" id="userBtn"
                aria-expanded="false" aria-haspopup="true">

            <div class="avatar">{{ $iniciales }}</div>

            <div class="user-info">
                <div class="user-name">{{ $nombres }}</div>
                <div class="user-role">{{ $rolEtiqueta }}</div>
            </div>

            {!! $icon('down') !!}
        </button>

        <div class="dropdown" id="userDropdown">
            <a href="{{ $urlPerfil }}" class="dd-item">
                {!! $icon('user') !!} {{ __('messages.profile') }}
            </a>

            <div class="dd-sep"></div>

            <form action="{{ url('/logout') }}" method="POST">
                @csrf
                <button type="submit" class="dd-item danger">
                    {!! $icon('logout') !!} {{ __('messages.logout') }}
                </button>
            </form>
        </div>
    </div>

</div>

        </header>

        <main class="content">
            @if (! $__env->hasSection('sin_cabecera'))
                <div class="page-head">
                    <div>
                        <h1>@yield('encabezado', 'Hola, ' . $nombres)</h1>
                        <p>@yield('subtitulo')</p>
                    </div>
                    <div class="page-actions">@yield('acciones')</div>
                </div>
            @endif

            @if (session('ok'))
                <div class="alert alert-ok">{{ session('ok') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-ok">{{ session('success') }}</div>
            @endif
            @if (session('status'))
                <div class="alert alert-ok">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-error">
                    <strong>Revisa estos datos:</strong>
                    <ul>
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('contenido')
        </main>

        <footer class="footer">Bella Flor POS · {{ $rolEtiqueta }}</footer>
    </div>

    <script>
        (() => {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const menuBtn = document.getElementById('menuBtn');
            const userBtn = document.getElementById('userBtn');
            const dropdown = document.getElementById('userDropdown');

            const closeMenu = () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
                menuBtn.setAttribute('aria-expanded', 'false');
            };

            menuBtn.addEventListener('click', () => {
                const abierto = sidebar.classList.toggle('open');
                overlay.classList.toggle('open', abierto);
                menuBtn.setAttribute('aria-expanded', String(abierto));
            });
            overlay.addEventListener('click', closeMenu);
            window.addEventListener('resize', () => { if (window.innerWidth > 860) closeMenu(); });

            userBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const abierto = dropdown.classList.toggle('open');
                userBtn.setAttribute('aria-expanded', String(abierto));
            });
            document.addEventListener('click', () => {
                dropdown.classList.remove('open');
                userBtn.setAttribute('aria-expanded', 'false');
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') { dropdown.classList.remove('open'); closeMenu(); }
            });
        })();
    </script>

    {{-- Contenedor de Toasts Flotantes para WebSockets (Reverb) --}}
    <div id="wsToastsContainer" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 12px; max-width: 400px; width: calc(100% - 48px); pointer-events: none;"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let notifCount = 0;
            const badge = document.getElementById('wsBadge');
            const dot = document.getElementById('wsStatusDot');
            const container = document.getElementById('wsToastsContainer');
            const bellBtn = document.getElementById('wsBellBtn');

            if (bellBtn) {
                bellBtn.addEventListener('click', () => {
                    notifCount = 0;
                    if (badge) badge.style.display = 'none';
                });
            }

            function playBeep(tipo) {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(tipo === 'alerta' ? 880 : 587.33, ctx.currentTime);
                    gain.gain.setValueAtTime(0.12, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.35);
                } catch(e) {}
            }

            function mostrarToast(titulo, mensaje, icono, tipo = 'info', enlace = null) {
                notifCount++;
                if (badge) {
                    badge.innerText = notifCount;
                    badge.style.display = 'inline-block';
                }
                playBeep(tipo);

                const toast = document.createElement('div');
                toast.style.cssText = `
                    pointer-events: auto;
                    background: #ffffff;
                    border-radius: 16px;
                    padding: 14px 18px;
                    box-shadow: 0 12px 32px rgba(59, 18, 48, 0.22);
                    border-left: 5px solid ${tipo === 'alerta' ? '#e53e3e' : '#38a169'};
                    display: flex;
                    align-items: flex-start;
                    gap: 12px;
                    animation: slideInToast .3s ease forwards;
                    font-family: inherit;
                `;

                toast.innerHTML = `
                    <div style="font-size: 26px; line-height: 1; flex-shrink: 0;">${icono}</div>
                    <div style="flex: 1;">
                        <div style="font-weight: 700; font-size: 13.5px; color: #2f2328; margin-bottom: 3px;">${titulo}</div>
                        <div style="font-size: 12.5px; color: #6f6166; line-height: 1.4;">${mensaje}</div>
                        ${enlace ? `<div style="margin-top: 6px;"><a href="${enlace}" style="color: #b83f61; font-weight: 600; font-size: 12px; text-decoration: underline;">Ver detalle →</a></div>` : ''}
                    </div>
                    <button type="button" style="background: none; border: none; font-size: 16px; color: #a3969b; cursor: pointer; padding: 0 4px;" onclick="this.parentElement.remove()">✕</button>
                `;

                container.appendChild(toast);

                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateX(20px)';
                        toast.style.transition = 'all .3s ease';
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 9000);
            }

            // Escuchar eventos en tiempo real con Laravel Echo vía Reverb
            function iniciarEcho() {
                if (typeof window.Echo !== 'undefined') {
                    if (dot) dot.style.background = '#38a169';

                    window.Echo.channel('pos-canal')
                        .listen('.StockBajo', (e) => {
                            mostrarToast(
                                '⚠️ ¡Alerta de Stock Bajo!',
                                e.mensaje || `El producto "${e.productoNombre}" tiene pocas unidades (${e.stockActual}).`,
                                '📦',
                                'alerta',
                                '{{ route("inventario.index") }}'
                            );
                        })
                        .listen('.VentaRealizada', (e) => {
                            mostrarToast(
                                '🛒 ¡Nueva Venta Registrada!',
                                e.mensaje || `Venta ${e.comprobante} por S/ ${Number(e.total).toFixed(2)} por ${e.vendedor}.`,
                                '💰',
                                'venta',
                                '{{ route("pos.index") }}'
                            );
                        });
                } else {
                    if (dot) dot.style.background = '#ecc94b';
                    // Reintentar si Echo tarda un instante en cargar
                    setTimeout(iniciarEcho, 500);
                }
            }

            iniciarEcho();
        });
    </script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
