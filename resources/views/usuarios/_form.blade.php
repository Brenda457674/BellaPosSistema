@php
    $u = $usuario ?? null;
    $esEdicion = isset($usuario);
    $esPropio = ($esEdicion && $usuario->id === auth()->id());
@endphp

<div class="form-grid">
    <div class="campo">
        <label>Nombres *</label>
        <input type="text" name="nombres" value="{{ old('nombres', $u->nombres ?? '') }}" placeholder="Ej. Ana María" required maxlength="100">
        @error('nombres')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Apellidos *</label>
        <input type="text" name="apellidos" value="{{ old('apellidos', $u->apellidos ?? '') }}" placeholder="Ej. Gómez Torres" required maxlength="100">
        @error('apellidos')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Correo electrónico (Gmail / Institucional) *</label>
        <input type="email" name="email" value="{{ old('email', $u->email ?? '') }}" placeholder="usuario@gmail.com" required maxlength="255">
        <small class="sub">Se enviará el enlace de verificación a este correo</small>
        @error('email')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Teléfono / Celular</label>
        <input type="text" name="telefono" value="{{ old('telefono', $u->telefono ?? '') }}" placeholder="Ej. 987654321" maxlength="25">
        @error('telefono')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Rol en el sistema *</label>
        @if ($esPropio)
            <select name="rol_id" style="background:var(--fondo); pointer-events:none;" tabindex="-1">
                @foreach ($roles as $r)
                    <option value="{{ $r->id }}" @selected(old('rol_id', $u->rol_id ?? '') == $r->id)>{{ $r->nombre }}</option>
                @endforeach
            </select>
            <small class="sub" style="color:var(--rosa-oscuro);">No puedes cambiar tu propio rol de Administrador.</small>
        @else
            <select name="rol_id" required>
                <option value="">-- Selecciona un rol --</option>
                @foreach ($roles as $r)
                    <option value="{{ $r->id }}" @selected(old('rol_id', $u->rol_id ?? '') == $r->id)>
                        {{ $r->nombre }} ({{ $r->descripcion ?: 'Acceso estándar' }})
                    </option>
                @endforeach
            </select>
        @endif
        @error('rol_id')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Estado de la cuenta *</label>
        @if ($esPropio)
            <select name="estado" style="background:var(--fondo); pointer-events:none;" tabindex="-1">
                <option value="activo" selected>Activo</option>
            </select>
            <small class="sub" style="color:var(--rosa-oscuro);">No puedes desactivar tu propia cuenta activa.</small>
        @else
            <select name="estado" required>
                <option value="activo" @selected(old('estado', $u->estado ?? 'activo') === 'activo')>Activo (Permite ingreso)</option>
                <option value="inactivo" @selected(old('estado', $u->estado ?? '') === 'inactivo')>Inactivo (Bloqueado)</option>
                <option value="pendiente" @selected(old('estado', $u->estado ?? '') === 'pendiente')>Pendiente de activación</option>
            </select>
        @endif
        @error('estado')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo full">
        <label>Contraseña {{ $esEdicion ? '(Dejar vacío para conservar la actual)' : '*' }}</label>
        <input type="password" name="password" placeholder="{{ $esEdicion ? '••••••••' : 'Mínimo 6 caracteres' }}" {{ $esEdicion ? '' : 'required' }} minlength="6">
        @error('password')<small class="error">{{ $message }}</small>@enderror
    </div>
</div>
