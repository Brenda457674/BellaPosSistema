<style>
    .barra { display:flex; gap:10px; flex-wrap:wrap; align-items:center; justify-content:space-between; margin-bottom:16px; }
    .buscar { display:flex; gap:8px; flex-wrap:wrap; }
    .buscar input, .campo input, .campo select, .campo textarea {
        width:100%; padding:10px 12px; border:1px solid var(--borde); border-radius:9px;
        background:#fff; color:var(--texto); font:inherit;
    }
    .buscar input { width:260px; max-width:100%; }
    .buscar input:focus, .campo input:focus, .campo select:focus, .campo textarea:focus {
        outline:none; border-color:var(--rosa); box-shadow:0 0 0 3px var(--rosa-suave);
    }
    .btn {
        display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border:1px solid transparent;
        border-radius:9px; font-weight:600; font-size:13px; text-decoration:none; cursor:pointer;
    }
    .btn-rosa { background:var(--rosa); color:#fff; }
    .btn-rosa:hover { background:var(--rosa-oscuro); }
    .btn-claro { background:#fff; color:var(--texto); border-color:var(--borde); }
    .btn-claro:hover { background:var(--fondo); }
    .btn-rojo { background:#fff; color:var(--rosa-oscuro); border-color:var(--rosa-borde); }
    .btn-rojo:hover { background:var(--rosa-suave); }
    .btn-sm { padding:6px 11px; font-size:12px; }
    .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
    .badge.ok { background:var(--verde-bg); color:var(--verde); }
    .badge.off { background:var(--fondo); color:var(--texto-3); }
    .alerta { padding:12px 16px; margin-bottom:16px; border-radius:10px; font-size:13px; }
    .alerta.ok { background:var(--verde-bg); color:var(--verde); }
    .alerta.error { background:var(--rosa-suave); color:var(--rosa-oscuro); }
    .form-grid { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:16px; }
    .campo label { display:block; margin-bottom:6px; font-size:12.5px; font-weight:600; color:var(--texto-2); }
    .campo.full { grid-column:1 / -1; }
    .error { display:block; margin-top:4px; font-size:12px; color:var(--rosa-oscuro); }
    .form-pie { display:flex; gap:10px; justify-content:flex-end; margin-top:22px; }
    .acciones-td { display:flex; gap:6px; }
    .acciones-td form { display:inline; }
    .paginacion { display:flex; align-items:center; justify-content:center; gap:14px; padding:16px; font-size:13px; color:var(--texto-2); }
    .sub { display:block; font-size:12px; color:var(--texto-3); margin-top:2px; }
    @media (max-width:700px) { .form-grid { grid-template-columns:1fr; } }
</style>
