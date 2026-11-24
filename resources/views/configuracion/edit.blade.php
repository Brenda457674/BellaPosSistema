@extends('layouts.panel')

@section('titulo', 'Configuración de la Tienda')
@section('encabezado', 'Configuración del Minimarket')
@section('subtitulo', 'Datos comerciales y fiscales que aparecen en los comprobantes de venta y tickets.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="card" style="max-width:850px; margin:0 auto;">
        <div class="card-body">
            <form method="POST" action="{{ route('configuracion.update') }}">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="campo">
                        <label>Nombre comercial de la tienda *</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $tienda->nombre ?? 'Bella Flor') }}" required maxlength="150" placeholder="Ej. Bella Flor POS">
                        @error('nombre')<small class="error">{{ $message }}</small>@enderror
                    </div>

                    <div class="campo">
                        <label>Razón social</label>
                        <input type="text" name="razon_social" value="{{ old('razon_social', $tienda->razon_social ?? '') }}" maxlength="180" placeholder="Ej. Minimarket Bella Flor S.A.C.">
                        @error('razon_social')<small class="error">{{ $message }}</small>@enderror
                    </div>

                    <div class="campo">
                        <label>Número de RUC</label>
                        <input type="text" name="ruc" value="{{ old('ruc', $tienda->ruc ?? '') }}" maxlength="11" placeholder="Ej. 20601234567">
                        @error('ruc')<small class="error">{{ $message }}</small>@enderror
                    </div>

                    <div class="campo">
                        <label>Teléfono / Celular de contacto</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $tienda->telefono ?? '') }}" maxlength="25" placeholder="Ej. (01) 234-5678">
                        @error('telefono')<small class="error">{{ $message }}</small>@enderror
                    </div>

                    <div class="campo full">
                        <label>Dirección del establecimiento</label>
                        <input type="text" name="direccion" value="{{ old('direccion', $tienda->direccion ?? '') }}" maxlength="255" placeholder="Ej. Av. Los Laureles 456, San Isidro - Lima">
                        @error('direccion')<small class="error">{{ $message }}</small>@enderror
                    </div>

                    <div class="campo">
                        <label>Correo electrónico oficial</label>
                        <input type="email" name="correo" value="{{ old('correo', $tienda->correo ?? '') }}" maxlength="150" placeholder="ventas@bellaflor.pe">
                        @error('correo')<small class="error">{{ $message }}</small>@enderror
                    </div>

                    <div class="campo">
                        <label>Moneda y símbolo *</label>
                        <div style="display:flex; gap:8px;">
                            <input type="text" name="moneda" value="{{ old('moneda', $tienda->moneda ?? 'PEN') }}" maxlength="3" placeholder="PEN" style="width:90px;" required>
                            <input type="text" name="simbolo_moneda" value="{{ old('simbolo_moneda', $tienda->simbolo_moneda ?? 'S/') }}" maxlength="5" placeholder="S/" style="width:70px;" required>
                        </div>
                        <small class="sub">Código ISO y símbolo impreso</small>
                        @error('moneda')<small class="error">{{ $message }}</small>@enderror
                        @error('simbolo_moneda')<small class="error">{{ $message }}</small>@enderror
                    </div>

                    <div class="campo full">
                        <label>Mensaje al pie del comprobante / ticket</label>
                        <input type="text" name="mensaje_comprobante" value="{{ old('mensaje_comprobante', $tienda->mensaje_comprobante ?? '¡Gracias por tu compra!') }}" maxlength="255" placeholder="Ej. ¡Gracias por tu compra! Conserve su comprobante.">
                        <small class="sub">Este texto se imprimirá en la parte inferior de cada ticket de venta.</small>
                        @error('mensaje_comprobante')<small class="error">{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="form-pie">
                    <button class="btn btn-rosa" type="submit">Guardar configuración</button>
                </div>
            </form>
        </div>
    </div>
@endsection
