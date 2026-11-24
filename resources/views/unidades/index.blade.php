@extends('layouts.panel')

@section('titulo', 'Unidades de Medida')
@section('encabezado', 'Unidades de Medida')
@section('subtitulo', 'Define cómo se cuantifican y fraccionan los productos (Unidades, Kilos, Litros).')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="barra">
        <form class="buscar" method="GET" action="{{ route('unidades.index') }}">
            <input type="text" name="q" value="{{ $q }}" placeholder="Buscar unidad o abreviatura...">
            <button class="btn btn-claro" type="submit">Buscar</button>
            @if ($q)
                <a class="btn btn-claro" href="{{ route('unidades.index') }}">Limpiar</a>
            @endif
        </form>

        <div style="display:flex; gap:8px;">
            <a class="btn btn-claro" href="{{ route('categorias.index') }}">📁 Ver categorías</a>
            <a class="btn btn-rosa" href="{{ route('unidades.create') }}">+ Nueva unidad</a>
        </div>
    </div>

    <div class="card">
        @if ($unidades->count())
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Abreviatura</th>
                            <th>Permite decimales</th>
                            <th>Productos vinculados</th>
                            <th style="text-align:right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($unidades as $u)
                            <tr>
                                <td><strong>{{ $u->nombre }}</strong></td>
                                <td>
                                    <span class="badge ok" style="background:var(--fondo); color:var(--texto); font-weight:700;">
                                        {{ $u->abreviatura }}
                                    </span>
                                </td>
                                <td>
                                    @if ($u->permite_decimales)
                                        <span class="badge ok">✓ Sí (Fraccionable)</span>
                                    @else
                                        <span class="badge off">Solo enteros</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $u->productos_count }} producto(s)
                                </td>
                                <td style="text-align:right;">
                                    <div class="acciones-td" style="justify-content: flex-end;">
                                        <a class="btn btn-claro btn-sm" href="{{ route('unidades.edit', $u->id) }}">Editar</a>

                                        @if ($puedeEliminar)
                                            <form method="POST" action="{{ route('unidades.destroy', $u->id) }}" onsubmit="return confirm('¿Eliminar la unidad de medida {{ $u->nombre }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-rojo btn-sm" type="submit">Eliminar</button>
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
                {{ $unidades->links() }}
            </div>
        @else
            <div class="empty">
                <div class="empty-icon">
                    <svg class="i" viewBox="0 0 24 24"><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                </div>
                <h3>No hay unidades de medida registradas</h3>
                <a class="btn btn-rosa" href="{{ route('unidades.create') }}">+ Crear unidad</a>
            </div>
        @endif
    </div>
@endsection
