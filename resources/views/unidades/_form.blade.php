@php $u = $unidad ?? null; @endphp

<div class="form-grid">
    <div class="campo">
        <label>Nombre de la unidad *</label>
        <input type="text" name="nombre" value="{{ old('nombre', $u->nombre ?? '') }}" placeholder="Ej. Kilogramo, Unidad, Litro, Paquete..." required maxlength="50">
        @error('nombre')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Abreviatura / Símbolo *</label>
        <input type="text" name="abreviatura" value="{{ old('abreviatura', $u->abreviatura ?? '') }}" placeholder="Ej. KG, UND, LT, PAQ..." required maxlength="15">
        @error('abreviatura')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo full">
        <label>¿Permite venta en cantidades decimales? *</label>
        <select name="permite_decimales" required>
            <option value="0" @selected(old('permite_decimales', $u->permite_decimales ?? 0) == 0)>
                No (Solo unidades enteras: 1, 2, 3...)
            </option>
            <option value="1" @selected(old('permite_decimales', $u->permite_decimales ?? 0) == 1)>
                Sí (Permite fraccionar: 0.500 kg, 1.250 kg, etc.)
            </option>
        </select>
        <small class="sub">El punto de venta validará esto al cobrar para evitar errores de despacho.</small>
        @error('permite_decimales')<small class="error">{{ $message }}</small>@enderror
    </div>
</div>
