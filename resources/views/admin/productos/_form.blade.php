
@php
    $p = $producto ?? null;
    $esEdicion = isset($producto);
@endphp

<div class="form-grid">
    <div class="campo">
        <label>{{ __('messages.internal_code') }} *</label>
        <input type="text" name="codigo" value="{{ old('codigo', $p->codigo ?? '') }}" placeholder="{{ __('messages.internal_code_placeholder') }}" required maxlength="50">
        @error('codigo')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>{{ __('messages.barcode') }}</label>
        <input type="text" name="codigo_barras" value="{{ old('codigo_barras', $p->codigo_barras ?? '') }}" placeholder="{{ __('messages.barcode_placeholder') }}" maxlength="100">
        @error('codigo_barras')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo full">
        <label>{{ __('messages.product_name') }} *</label>
        <input type="text" name="nombre" value="{{ old('nombre', $p->nombre ?? '') }}" placeholder="{{ __('messages.product_name_placeholder') }}" required maxlength="150">
        @error('nombre')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>{{ __('messages.category') }} *</label>
        <select name="categoria_id" required>
            <option value="">{{ __('messages.select_category') }}</option>
            @foreach ($categorias as $cat)
                <option value="{{ $cat->id }}" @selected(old('categoria_id', $p->categoria_id ?? '') == $cat->id)>
                    {{ $cat->nombre }}
                </option>
            @endforeach
        </select>
        @error('categoria_id')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>{{ __('messages.unit_of_measure') }} *</label>
        <select name="unidad_medida_id" required>
            <option value="">{{ __('messages.select_unit') }}</option>
            @foreach ($unidades as $u)
                <option value="{{ $u->id }}" @selected(old('unidad_medida_id', $p->unidad_medida_id ?? '') == $u->id)>
                    {{ $u->nombre ?? $u->abreviatura }} ({{ $u->abreviatura ?? '' }})
                </option>
            @endforeach
        </select>
        @error('unidad_medida_id')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>{{ __('messages.brand') }}</label>
        <input type="text" name="marca" value="{{ old('marca', $p->marca ?? '') }}" placeholder="{{ __('messages.brand_placeholder') }}" maxlength="100">
        @error('marca')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>{{ __('messages.status') }} *</label>
        <select name="estado" required>
            <option value="activo" @selected(old('estado', $p->estado ?? 'activo') === 'activo')>{{ __('messages.active_available') }}</option>
            <option value="inactivo" @selected(old('estado', $p->estado ?? '') === 'inactivo')>{{ __('messages.inactive_hidden') }}</option>
        </select>
        @error('estado')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>{{ __('messages.purchase_price') }} (S/) *</label>
        <input type="number" step="0.01" min="0" name="precio_compra" value="{{ old('precio_compra', $p->precio_compra ?? '0.00') }}" required placeholder="0.00">
        <small class="sub">{{ __('messages.purchase_cost_help') }}</small>
        @error('precio_compra')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>{{ __('messages.public_sale_price') }} (S/) *</label>
        <input type="number" step="0.01" min="0" name="precio_venta" value="{{ old('precio_venta', $p->precio_venta ?? '') }}" required placeholder="0.00">
        <small class="sub">{{ __('messages.sale_price_help') }}</small>
        @error('precio_venta')<small class="error">{{ $message }}</small>@enderror
    </div>

    @if (!$esEdicion)
        <div class="campo">
            <label>{{ __('messages.initial_stock') }} *</label>
            <input type="number" step="0.001" min="0" name="stock_actual" value="{{ old('stock_actual', '0') }}" required placeholder="0">
            <small class="sub">{{ __('messages.initial_stock_help') }}</small>
            @error('stock_actual')<small class="error">{{ $message }}</small>@enderror
        </div>
    @else
        <div class="campo">
            <label>{{ __('messages.current_stock_readonly') }}</label>
            <input type="text" value="{{ number_format($p->stock_actual, 3) }}" disabled style="background:var(--fondo); color:var(--texto-2);">
            <small class="sub">{{ __('messages.stock_edit_help') }}</small>
        </div>
    @endif

    <div class="campo">
        <label>{{ __('messages.minimum_stock_alert') }} *</label>
        <input type="number" step="0.001" min="0" name="stock_minimo" value="{{ old('stock_minimo', $p->stock_minimo ?? '5') }}" required placeholder="5">
        <small class="sub">{{ __('messages.minimum_stock_help') }}</small>
        @error('stock_minimo')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo full">
        <label>{{ __('messages.expiration_control') }}</label>
        <select name="controla_vencimiento">
            <option value="0" @selected(!old('controla_vencimiento', $p->controla_vencimiento ?? 0))>{{ __('messages.no_perishable') }}</option>
            <option value="1" @selected(old('controla_vencimiento', $p->controla_vencimiento ?? 0) == 1)>{{ __('messages.yes_perishable') }}</option>
        </select>
        @error('controla_vencimiento')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo full">
        <label>{{ __('messages.description_observations') }}</label>
        <textarea name="descripcion" rows="3" placeholder="{{ __('messages.description_placeholder') }}">{{ old('descripcion', $p->descripcion ?? '') }}</textarea>
        @error('descripcion')<small class="error">{{ $message }}</small>@enderror
    </div>
</div>
