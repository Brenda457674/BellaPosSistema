@php $p = $proveedor ?? null; @endphp

<div class="form-grid">
    <div class="campo">
        <label>Tipo de documento</label>
        <select name="tipo_documento">
            @foreach (['RUC', 'DNI', 'OTRO'] as $t)
                <option value="{{ $t }}" @selected(old('tipo_documento', $p->tipo_documento ?? 'RUC') === $t)>{{ $t }}</option>
            @endforeach
        </select>
        @error('tipo_documento')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Número de documento</label>
        <input type="text" name="numero_documento" value="{{ old('numero_documento', $p->numero_documento ?? '') }}" maxlength="20">
        @error('numero_documento')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo full">
        <label>Razón social / Nombre *</label>
        <input type="text" name="razon_social" value="{{ old('razon_social', $p->razon_social ?? '') }}" maxlength="150" required>
        @error('razon_social')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Persona de contacto</label>
        <input type="text" name="contacto" value="{{ old('contacto', $p->contacto ?? '') }}" maxlength="120">
        @error('contacto')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Teléfono</label>
        <input type="text" name="telefono" value="{{ old('telefono', $p->telefono ?? '') }}" maxlength="25">
        @error('telefono')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Correo</label>
        <input type="email" name="email" value="{{ old('email', $p->email ?? '') }}" maxlength="150">
        @error('email')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Estado</label>
        <select name="estado">
            @foreach (['activo', 'inactivo'] as $e)
                <option value="{{ $e }}" @selected(old('estado', $p->estado ?? 'activo') === $e)>{{ ucfirst($e) }}</option>
            @endforeach
        </select>
        @error('estado')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo full">
        <label>Dirección</label>
        <input type="text" name="direccion" value="{{ old('direccion', $p->direccion ?? '') }}" maxlength="255">
        @error('direccion')<small class="error">{{ $message }}</small>@enderror
    </div>
</div>
