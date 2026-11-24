@extends('layouts.panel')

@section('titulo', 'Ajuste de inventario')
@section('encabezado', 'Registrar ajuste de inventario')
@section('subtitulo', 'Modifica manualmente las existencias por conteo físico, merma, rotura o devolución.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="card" style="max-width:750px; margin:0 auto;">
        <div class="card-body">
            <form method="POST" action="{{ route('inventario.ajuste.store') }}">
                @csrf

                <div class="form-grid">
                    <div class="campo full">
                        <label>Producto a ajustar *</label>
                        <select name="producto_id" id="selProducto" required>
                            <option value="">-- Selecciona el producto --</option>
                            @foreach ($productos as $prod)
                                <option value="{{ $prod->id }}" data-stock="{{ $prod->stock_actual }}" @selected(old('producto_id', $productoSeleccionadoId) == $prod->id)>
                                    [{{ $prod->codigo }}] {{ $prod->nombre }} · (Stock actual: {{ number_format($prod->stock_actual, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('producto_id')<small class="error">{{ $message }}</small>@enderror
                    </div>

                    <div class="campo">
                        <label>Tipo de ajuste *</label>
                        <select name="tipo" id="selTipo" required>
                            <option value="ajuste_positivo" @selected(old('tipo') === 'ajuste_positivo')>➕ Ajuste Positivo (Aumentar stock)</option>
                            <option value="ajuste_negativo" @selected(old('tipo') === 'ajuste_negativo')>➖ Ajuste Negativo (Disminuir stock)</option>
                        </select>
                        @error('tipo')<small class="error">{{ $message }}</small>@enderror
                    </div>

                    <div class="campo">
                        <label>Cantidad a ajustar *</label>
                        <input type="number" step="0.001" min="0.001" name="cantidad" value="{{ old('cantidad') }}" placeholder="Ej. 2 o 0.5" required>
                        @error('cantidad')<small class="error">{{ $message }}</small>@enderror
                    </div>

                    <div class="campo full">
                        <label>Motivo / Justificación *</label>
                        <input type="text" name="motivo" value="{{ old('motivo') }}" placeholder="Ej. Conteo físico fin de mes, merma por rotura, producto vencido..." required maxlength="255">
                        <small class="sub">Este motivo quedará registrado permanentemente en el Kardex.</small>
                        @error('motivo')<small class="error">{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="form-pie">
                    <a class="btn btn-claro" href="{{ route('inventario.index') }}">Cancelar</a>
                    <button class="btn btn-rosa" type="submit">Guardar ajuste y actualizar Kardex</button>
                </div>
            </form>
        </div>
    </div>
@endsection
