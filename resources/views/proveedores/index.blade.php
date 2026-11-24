@extends('layouts.panel')

@section('titulo', 'Proveedores')
@section('encabezado', 'Proveedores')
@section('subtitulo', 'Quienes te venden la mercadería.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    @if (session('ok'))
        <div class="alerta ok">{{ session('ok') }}</div>
    @endif

    <div class="barra">
        <form class="buscar" method="GET" action="{{ route('proveedores.index') }}">
            <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, documento o contacto">
            <button class="btn btn-claro" type="submit">Buscar</button>
        </form>
        <a class="btn btn-rosa" href="{{ route('proveedores.create') }}">+ Nuevo proveedor</a>
    </div>

    <div class="card">
        @if ($proveedores->count())
            <table>
                <thead>
                    <tr>
                        <th>Proveedor</th>
                        <th>Documento</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($proveedores as $p)
                        <tr>
                            <td>
                                <strong>{{ $p->razon_social }}</strong>
                                @if ($p->direccion)<span class="sub">{{ $p->direccion }}</span>@endif
                            </td>
                            <td>{{ $p->numero_documento ? $p->tipo_documento . ' ' . $p->numero_documento : '—' }}</td>
                            <td>
                                {{ $p->contacto ?: '—' }}
                                @if ($p->telefono)<span class="sub">{{ $p->telefono }}</span>@endif
                                @if ($p->email)<span class="sub">{{ $p->email }}</span>@endif
                            </td>
                            <td>
                                <span class="badge {{ $p->estado === 'activo' ? 'ok' : 'off' }}">{{ ucfirst($p->estado) }}</span>
                            </td>
                            <td>
                                <div class="acciones-td">
                                    <a class="btn btn-claro btn-sm" href="{{ route('proveedores.edit', $p->id) }}">Editar</a>
                                    <form method="POST" action="{{ route('proveedores.destroy', $p->id) }}"
                                          onsubmit="return confirm('¿Eliminar este proveedor?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-rojo btn-sm" type="submit">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($proveedores->hasPages())
                <div class="paginacion">
                    @if ($proveedores->previousPageUrl())
                        <a class="btn btn-claro btn-sm" href="{{ $proveedores->previousPageUrl() }}">← Anterior</a>
                    @endif
                    <span>Página {{ $proveedores->currentPage() }} de {{ $proveedores->lastPage() }}</span>
                    @if ($proveedores->nextPageUrl())
                        <a class="btn btn-claro btn-sm" href="{{ $proveedores->nextPageUrl() }}">Siguiente →</a>
                    @endif
                </div>
            @endif
        @else
            <div class="empty">
                <h3>Aún no hay proveedores</h3>
                <p>Registra tu primer proveedor para poder cargar compras.</p>
            </div>
        @endif
    </div>
@endsection
