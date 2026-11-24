@php
    $c = $cliente ?? null;
@endphp

<div class="form-grid">
    <div class="campo">
        <label>Tipo de documento</label>
        <select name="tipo_documento" required>
            @foreach (['DNI', 'RUC', 'CE', 'OTRO'] as $t)
                <option value="{{ $t }}" @selected(old('tipo_documento', $c->tipo_documento ?? 'DNI') === $t)>{{ $t }}</option>
            @endforeach
        </select>
        @error('tipo_documento')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Número de documento</label>
        <input type="text" name="numero_documento" value="{{ old('numero_documento', $c->numero_documento ?? '') }}" placeholder="Ej. 74859612" maxlength="20">
        @error('numero_documento')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Nombres *</label>
        <input type="text" name="nombres" value="{{ old('nombres', $c->nombres ?? '') }}" placeholder="Nombres del cliente" maxlength="120" required>
        @error('nombres')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Apellidos</label>
        <input type="text" name="apellidos" value="{{ old('apellidos', $c->apellidos ?? '') }}" placeholder="Apellidos del cliente" maxlength="120">
        @error('apellidos')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Teléfono / Celular</label>
        <input type="text" name="telefono" value="{{ old('telefono', $c->telefono ?? '') }}" placeholder="Ej. 987654321" maxlength="25">
        @error('telefono')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Correo electrónico</label>
        <input type="email" name="email" value="{{ old('email', $c->email ?? '') }}" placeholder="cliente@correo.com" maxlength="150">
        @error('email')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Estado</label>
        <select name="estado" required>
            <option value="activo" @selected(old('estado', $c->estado ?? 'activo') === 'activo')>Activo</option>
            <option value="inactivo" @selected(old('estado', $c->estado ?? '') === 'inactivo')>Inactivo</option>
        </select>
        @error('estado')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo full">
        <label>Dirección</label>
        <input type="text" name="direccion" value="{{ old('direccion', $c->direccion ?? '') }}" placeholder="Dirección o referencia del cliente" maxlength="255">
        @error('direccion')<small class="error">{{ $message }}</small>@enderror
    </div>
</div>
