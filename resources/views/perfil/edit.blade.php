@extends('layouts.panel')

@section('titulo', __('messages.profile') ?? 'Mi perfil')
@section('encabezado', __('messages.profile') ?? 'Mi perfil')
@section('subtitulo', 'Gestiona tus datos personales, contraseña y preferencias de seguridad.')

@push('styles')
    @include('partials.ui')
    <style>
        .perfil-container {
            display: grid;
            grid-template-columns: 320px minmax(0, 1fr);
            gap: 24px;
            align-items: start;
        }

        .perfil-card-user {
            background: #fff;
            border: 1px solid var(--borde);
            border-radius: 18px;
            padding: 28px 22px;
            text-align: center;
            box-shadow: var(--sombra);
        }

        .perfil-avatar-big {
            width: 90px;
            height: 90px;
            margin: 0 auto 16px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--rosa), var(--rosa-oscuro));
            color: #fff;
            font-size: 32px;
            font-weight: 700;
            box-shadow: 0 10px 24px rgba(214, 90, 122, .35);
        }

        .perfil-user-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--ciruela);
            line-height: 1.3;
        }

        .perfil-user-email {
            font-size: 13px;
            color: var(--texto-2);
            margin-top: 4px;
            word-break: break-all;
        }

        .perfil-badges {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 14px;
            flex-wrap: wrap;
        }

        .perfil-info-list {
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid var(--borde);
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 12px;
            font-size: 13px;
        }

        .perfil-info-item {
            display: flex;
            justify-content: space-between;
            color: var(--texto-2);
        }

        .perfil-info-item strong {
            color: var(--ciruela);
        }

        .perfil-forms-column {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .con-ojo {
            position: relative;
        }

        .con-ojo input {
            padding-right: 44px;
        }

        .btn-ojo {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: 0;
            padding: 6px;
            cursor: pointer;
            color: var(--texto-3);
            display: grid;
            place-items: center;
            border-radius: 8px;
            transition: color .15s, background .15s;
        }

        .btn-ojo:hover {
            color: var(--rosa);
            background: var(--rosa-suave);
        }

        .btn-ojo svg {
            width: 18px;
            height: 18px;
        }

        @media (max-width: 900px) {
            .perfil-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('contenido')

    <div class="perfil-container">
        <!-- COLUMNA IZQUIERDA: RESUMEN DEL USUARIO -->
        <aside class="perfil-card-user">
            @php
                $nombresStr = $usuario->nombres ?? $usuario->name ?? 'Usuario';
                $apellidosStr = $usuario->apellidos ?? '';
                $inits = strtoupper(mb_substr($nombresStr, 0, 1) . mb_substr($apellidosStr ?: $nombresStr, 0, 1));
                $rolNom = $usuario->rol?->nombre ?? 'Usuario';
            @endphp

            <div class="perfil-avatar-big">
                {{ $inits }}
            </div>

            <h2 class="perfil-user-name">{{ $usuario->name }}</h2>
            <div class="perfil-user-email">{{ $usuario->email }}</div>

            <div class="perfil-badges">
                <span class="badge" style="background: linear-gradient(135deg, var(--ciruela), var(--ciruela-2)); color: #fff; font-weight: 600; padding: 4px 12px;">
                    {{ $rolNom }}
                </span>

                @if ($usuario->estado === 'activo')
                    <span class="badge ok">● Activo</span>
                @else
                    <span class="badge off">● {{ ucfirst($usuario->estado ?? 'Inactivo') }}</span>
                @endif
            </div>

            <div class="perfil-info-list">
                <div class="perfil-info-item">
                    <span>Estado email:</span>
                    @if ($usuario->hasVerifiedEmail())
                        <span style="color: var(--verde); font-weight: 600;">✓ Verificado</span>
                    @else
                        <span style="color: #b7791f; font-weight: 600;">⚠ No verificado</span>
                    @endif
                </div>

                <div class="perfil-info-item">
                    <span>Teléfono:</span>
                    <strong>{{ $usuario->telefono ?: 'No registrado' }}</strong>
                </div>

                <div class="perfil-info-item">
                    <span>Registrado:</span>
                    <strong>{{ $usuario->created_at ? $usuario->created_at->format('d/m/Y') : '-' }}</strong>
                </div>
            </div>
        </aside>

        <!-- COLUMNA DERECHA: FORMULARIOS -->
        <div class="perfil-forms-column">

            <!-- 1. DATOS PERSONALES -->
            <div class="card">
                <div class="card-head">
                    <h2>Datos personales</h2>
                    <span>Información de tu cuenta</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('perfil.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-grid">
                            <div class="campo">
                                <label for="nombres">Nombres *</label>
                                <input
                                    type="text"
                                    id="nombres"
                                    name="nombres"
                                    value="{{ old('nombres', $usuario->nombres) }}"
                                    required
                                    maxlength="100"
                                >
                                @error('nombres')<small class="error">{{ $message }}</small>@enderror
                            </div>

                            <div class="campo">
                                <label for="apellidos">Apellidos *</label>
                                <input
                                    type="text"
                                    id="apellidos"
                                    name="apellidos"
                                    value="{{ old('apellidos', $usuario->apellidos) }}"
                                    required
                                    maxlength="100"
                                >
                                @error('apellidos')<small class="error">{{ $message }}</small>@enderror
                            </div>

                            <div class="campo full">
                                <label for="email">Correo electrónico *</label>

                                <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                                    @if ($usuario->hasVerifiedEmail())
                                        <span class="badge ok">
                                            ✓ Correo verificado
                                        </span>
                                    @else
                                        <span class="badge" style="background:#fff3cd; color:#856404; border:1px solid #ffe69c;">
                                            ⚠ Correo no verificado
                                        </span>
                                    @endif
                                </div>

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email', $usuario->email) }}"
                                    required
                                    maxlength="150"
                                >
                                <small class="sub">Si cambias tu correo electrónico, deberás volver a verificar la nueva dirección.</small>
                                @error('email')<small class="error">{{ $message }}</small>@enderror
                            </div>

                            <div class="campo full">
                                <label for="telefono">Teléfono / Celular</label>
                                <input
                                    type="text"
                                    id="telefono"
                                    name="telefono"
                                    value="{{ old('telefono', $usuario->telefono) }}"
                                    maxlength="20"
                                    placeholder="Ej. 999 999 999"
                                >
                                @error('telefono')<small class="error">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="form-pie">
                            <button type="submit" class="btn btn-rosa">
                                Guardar cambios
                            </button>
                        </div>
                    </form>

                    @unless ($usuario->hasVerifiedEmail())
                        <div style="margin-top: 18px; padding-top: 14px; border-top: 1px dashed var(--borde); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                            <span style="font-size: 13px; color: var(--texto-2);">¿No recibiste el correo de confirmación?</span>
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-claro btn-sm">
                                    Reenviar correo de verificación
                                </button>
                            </form>
                        </div>
                    @endunless
                </div>
            </div>

            <!-- 2. CAMBIAR CONTRASEÑA -->
            <div class="card">
                <div class="card-head">
                    <h2>Seguridad y Contraseña</h2>
                    <span>Actualiza tu clave de acceso</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('perfil.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="campo" style="margin-bottom: 16px;">
                            <label for="current_password">Contraseña actual *</label>
                            <div class="con-ojo">
                                <input
                                    type="password"
                                    id="current_password"
                                    name="current_password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Ingresa tu contraseña actual"
                                >
                                <button type="button" class="btn-ojo" onclick="verClave('current_password', this)" aria-label="Mostrar contraseña">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                            @error('current_password')<small class="error">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-grid">
                            <div class="campo">
                                <label for="password">Nueva contraseña *</label>
                                <div class="con-ojo">
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        required
                                        minlength="8"
                                        autocomplete="new-password"
                                        placeholder="Mínimo 8 caracteres"
                                    >
                                    <button type="button" class="btn-ojo" onclick="verClave('password', this)" aria-label="Mostrar contraseña">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                                @error('password')<small class="error">{{ $message }}</small>@enderror
                            </div>

                            <div class="campo">
                                <label for="password_confirmation">Confirmar nueva contraseña *</label>
                                <div class="con-ojo">
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        required
                                        minlength="8"
                                        autocomplete="new-password"
                                        placeholder="Repite la nueva contraseña"
                                    >
                                    <button type="button" class="btn-ojo" onclick="verClave('password_confirmation', this)" aria-label="Mostrar contraseña">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-pie">
                            <button type="submit" class="btn btn-claro">
                                Actualizar contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 3. ZONA DE PELIGRO: ELIMINAR CUENTA (MENOS ADMINISTRADORES) -->
            @if ($usuario->rol?->nombre === 'Administrador')
                {{-- AVISO INFORMATIVO PARA ADMINISTRADORES --}}
                <div class="card" style="border-left: 4px solid var(--rosa);">
                    <div class="card-head">
                        <h2 style="display:flex; align-items:center; gap:8px; color:var(--ciruela);">
                            <svg class="i" style="color:var(--rosa);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            Cuenta de Administrador Protegida
                        </h2>
                        <span class="badge" style="background:var(--rosa-suave); color:var(--rosa-oscuro);">Seguridad del sistema</span>
                    </div>
                    <div class="card-body">
                        <p style="color:var(--texto-2); line-height:1.6; font-size:13.5px;">
                            Por políticas de seguridad de <strong>Bella Flor POS</strong>, las cuentas con rol de <strong>Administrador</strong> no pueden eliminarse a sí mismas desde el perfil personal para evitar que el sistema quede sin administración.
                        </p>
                        <p style="margin-top:8px; font-size:12.5px; color:var(--texto-3);">
                            Si deseas administrar, dar de baja o transferir roles a otros usuarios, puedes hacerlo desde el módulo de
                            @if (\Illuminate\Support\Facades\Route::has('usuarios.index'))
                                <a href="{{ route('usuarios.index') }}" style="color:var(--rosa); font-weight:600; text-decoration:underline;">Gestión de Usuarios</a>.
                            @else
                                <strong>Usuarios</strong>.
                            @endif
                        </p>
                    </div>
                </div>
            @else
                {{-- OPCIÓN DE ELIMINAR CUENTA PARA OTROS ROLES (VENDEDOR, ALMACENERO, ETC.) --}}
                <div class="card" style="border-left: 4px solid #c0344f;">
                    <div class="card-head">
                        <h2 style="display:flex; align-items:center; gap:8px; color:#c0344f;">
                            <svg class="i" style="color:#c0344f;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                            Zona de peligro: Eliminar mi cuenta
                        </h2>
                        <span class="badge" style="background:#fdecef; color:#c0344f;">Irreversible</span>
                    </div>
                    <div class="card-body">
                        <p style="color:var(--texto-2); line-height:1.6; font-size:13.5px; margin-bottom:18px;">
                            Si decides eliminar tu cuenta, perderás inmediatamente el acceso al sistema de Bella Flor POS. Esta acción no se puede deshacer.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('perfil.destroy') }}"
                            onsubmit="return confirm('¿Estás totalmente seguro/a de que deseas eliminar tu cuenta? Esta acción es definitiva e irreversible.');"
                        >
                            @csrf
                            @method('DELETE')

                            <div class="campo" style="max-width:440px; margin-bottom:18px;">
                                <label for="delete_password">Para continuar, confirma con tu contraseña actual *</label>
                                <div class="con-ojo">
                                    <input
                                        type="password"
                                        id="delete_password"
                                        name="password"
                                        required
                                        autocomplete="current-password"
                                        placeholder="Tu contraseña actual"
                                    >
                                    <button type="button" class="btn-ojo" onclick="verClave('delete_password', this)" aria-label="Mostrar contraseña">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                                @error('password')<small class="error">{{ $message }}</small>@enderror
                            </div>

                            <button
                                type="submit"
                                class="btn btn-rojo"
                                style="background:#c0344f; color:#fff; border-color:#c0344f;"
                            >
                                <svg class="i" style="width:16px; height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                Eliminar mi cuenta definitivamente
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <script>
        function verClave(idInput, btn) {
            const input = document.getElementById(idInput);
            if (!input) return;
            const mostrar = input.type === 'password';
            input.type = mostrar ? 'text' : 'password';
            btn.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');
            btn.style.color = mostrar ? 'var(--rosa)' : '';
        }
    </script>

@endsection
