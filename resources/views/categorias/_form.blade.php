@php $c = $categoria ?? null; @endphp

<div class="form-grid">
    <div class="campo full">
        <label>Nombre de la categoría *</label>
        <input type="text" name="nombre" value="{{ old('nombre', $c->nombre ?? '') }}" placeholder="Ej. Lácteos, Bebidas, Abarrotes..." required maxlength="100">
        @error('nombre')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo">
        <label>Estado *</label>
        <select name="estado" required>
            <option value="activa" @selected(old('estado', $c->estado ?? 'activa') === 'activa')>Activa</option>
            <option value="inactiva" @selected(old('estado', $c->estado ?? '') === 'inactiva')>Inactiva</option>
        </select>
        @error('estado')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="campo full">
        <label>Descripción</label>
        <textarea name="descripcion" rows="3" placeholder="Descripción breve de los productos que agrupa...">{{ old('descripcion', $c->descripcion ?? '') }}</textarea>
        @error('descripcion')<small class="error">{{ $message }}</small>@enderror
    </div>
</div>
