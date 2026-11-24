@extends('layouts.panel')

@section('titulo', 'Usuarios')
@section('encabezado', 'Gestión de Usuarios')
@section('subtitulo', 'Administra cuentas, asignación de roles y permisos del personal.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="barra">
        <form class="buscar" method="GET" action="{{ route('usuarios.index') }}">
            <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre o correo...">

            <select name="rol_id" onchange="this.form.submit()" style="padding:10px 12px; border:1px solid var(--borde); border-radius:9px; background:#fff; font:inherit;">
                <option value="">Todos los roles</option>
                @foreach ($roles as $r)
                    <option value="{{ $r->id }}" @selected($rolId == $r->id)>{{ $r->nombre }}</option>
                @endforeach
            </select>

            <select name="estado" onchange="this.form.submit()" style="padding:10px 12px; border:1px solid var(--borde); border-radius:9px; background:#fff; font:inherit;">
                <option value="">Todos los estados</option>
                <option value="activo" @selected($estado === 'activo')>Activo</option>
                <option value="inactivo" @selected($estado === 'inactivo')>Inactivo</option>
                <option value="pendiente" @selected($estado === 'pendiente')>Pendiente</option>
            </select>

            <button class="btn btn-claro" type="submit">Buscar</button>
            @if ($q || $rolId || $estado)
                <a class="btn btn-claro" href="{{ route('usuarios.index') }}">Limpiar</a>
            @endif
        </form>

        <a class="btn btn-rosa" href="{{ route('usuarios.create') }}">+ Nuevo usuario</a>
    </div>

    <div class="card">
        @if ($usuarios->count())
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Correo / Teléfono</th>
                            <th>Rol asignado</th>
                            <th>Correo verificado</th>
                            <th>Estado</th>
                            <th style="text-align:right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuarios as $u)
                            @php
                                $esMiCuenta = ($u->id === auth()->id());
                                $rolBadgeStyle = match($u->rol?->nombre) {
                                    'Administrador' => 'background:#fee2e2; color:#b91c1c;',
                                    'Vendedor'      => 'background:#e0e7ff; color:#3730a3;',
                                    'Almacenero'    => 'background:#fef3c7; color:#92400e;',
                                    default         => 'background:var(--fondo); color:var(--texto);'
                                };
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $u->name }}</strong>
                                    @if ($esMiCuenta)
                                        <span class="badge ok" style="background:var(--rosa-suave); color:var(--rosa-oscuro); font-size:10px; margin-left:4px;">Tú</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $u->email }}</div>
                                    @if ($u->telefono)<div class="sub">📞 {{ $u->telefono }}</div>@endif
                                </td>
                                <td>
                                    <span class="badge" style="{{ $rolBadgeStyle }} font-weight:700;">
                                        {{ $u->rol?->nombre ?? 'Sin rol' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($u->hasVerifiedEmail())
                                        <span class="badge ok">✓ Verificado</span>
                                    @else
                                        <span class="badge off" title="Aún no confirma el correo de verificación">Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $u->estado === 'activo' ? 'ok' : 'off' }}">
                                        {{ ucfirst($u->estado) }}
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <div class="acciones-td" style="justify-content: flex-end;">
                                        <a class="btn btn-claro btn-sm" href="{{ route('usuarios.edit', $u->id) }}">Editar</a>

                                        @if (!$esMiCuenta)
                                            <form method="POST" action="{{ route('usuarios.destroy', $u->id) }}" onsubmit="return confirm('¿Seguro que deseas cambiar el estado de este usuario?')">
                                                @csrf
                                                @method('DELETE')
                                                @if ($u->estado === 'activo')
                                                    <button class="btn btn-rojo btn-sm" type="submit">Desactivar</button>
                                                @else
                                                    <button class="btn btn-claro btn-sm" style="color:var(--verde); border-color:#86efac;" type="submit">Activar</button>
                                                @endif
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="paginacion">
                {{ $usuarios->links() }}
            </div>
        @else
            <div class="empty">
                <div class="empty-icon">
                    <svg class="i" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>No se encontraron usuarios</h3>
                <p>Prueba buscando con otros términos o filtros.</p>
            </div>
        @endif
    </div>
@endsection
