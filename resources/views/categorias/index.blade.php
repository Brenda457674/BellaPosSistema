@extends('layouts.panel')

@section('titulo', __('messages.categories'))
@section('encabezado', __('messages.categories'))
@section('subtitulo', __('messages.categories_subtitle'))

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="barra">
        <form class="buscar" method="GET" action="{{ route('categorias.index') }}">
            <input type="text" name="q" value="{{ $q }}" placeholder="Buscar categoría...">
            <button class="btn btn-claro" type="submit">{{ __('messages.search') }}</button>
            @if ($q)
                <a class="btn btn-claro" href="{{ route('categorias.index') }}">{{ __('messages.clear') }}</a>
            @endif
        </form>

        <div style="display:flex; gap:8px;">
            <a class="btn btn-claro" href="{{ route('unidades.index') }}">⚖️ {{ __('messages.units') }}</a>
            <a class="btn btn-rosa" href="{{ route('categorias.create') }}">+ {{ __('messages.new_category') }}</a>
        </div>
    </div>

    <div class="card">
        @if ($categorias->count())
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.description') }}</th>
                            <th>{{ __('messages.products') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th style="text-align:right;">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categorias as $cat)
                            <tr>
                                <td>
                                    <strong>{{ $cat->nombre }}</strong>
                                </td>
                                <td>
                                    {{ $cat->descripcion ?: '—' }}
                                </td>
                                <td>
                                    <span class="badge ok" style="background:var(--fondo); color:var(--texto);">
                                        {{ $cat->productos_count }} producto(s)
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $cat->estado === 'activa' ? 'ok' : 'off' }}">
                                        {{ ucfirst($cat->estado) }}
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <div class="acciones-td" style="justify-content: flex-end;">
                                        <a class="btn btn-claro btn-sm" href="{{ route('categorias.edit', $cat->id) }}">Editar</a>

                                        @if ($puedeEliminar)
                                            <form method="POST" action="{{ route('categorias.destroy', $cat->id) }}" onsubmit="return confirm('¿Eliminar la categoría {{ $cat->nombre }}?')">
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
                {{ $categorias->links() }}
            </div>
        @else
            <div class="empty">
                <div class="empty-icon">
                    <svg class="i" viewBox="0 0 24 24"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                </div>
                <h3>No hay categorías registradas</h3>
                <a class="btn btn-rosa" href="{{ route('categorias.create') }}">+ Crear categoría</a>
            </div>
        @endif
    </div>
@endsection
