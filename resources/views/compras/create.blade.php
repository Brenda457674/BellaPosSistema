@extends('layouts.panel')

@section('titulo', 'Registrar compra')
@section('encabezado', 'Registrar compra de mercadería')
@section('subtitulo', 'Ingresa los productos comprados para actualizar automáticamente el stock y registrar el ingreso en Kardex.')

@push('styles')
    @include('partials.ui')
    <style>
        .seccion-titulo { font-size: 15px; font-weight: 700; color: var(--texto); margin: 24px 0 14px; padding-bottom: 8px; border-bottom: 1px solid var(--borde); display: flex; justify-content: space-between; align-items: center; }
        .grid-agregar { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 12px; align-items: end; background: var(--fondo); padding: 16px; border-radius: 12px; margin-bottom: 16px; }
        .totales-box { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; margin-top: 20px; font-size: 14px; }
        .totales-fila { display: flex; gap: 24px; min-width: 260px; justify-content: space-between; }
        .totales-fila.gran-total { font-size: 17px; font-weight: 700; color: var(--rosa-oscuro); border-top: 2px solid var(--borde); padding-top: 8px; }
        @media (max-width: 800px) { .grid-agregar { grid-template-columns: 1fr; } }
    </style>
@endpush

@section('contenido')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('compras.store') }}" id="formCompra">
                @csrf

                <div class="seccion-titulo" style="margin-top:0;">
                    <span>1. Datos del comprobante</span>
                </div>

                <div class="form-grid">
                    <div class="campo">
                        <label>Proveedor *</label>
                        <select name="proveedor_id" required>
                            <option value="">-- Selecciona el proveedor --</option>
                            @foreach ($proveedores as $p)
                                <option value="{{ $p->id }}" @selected(old('proveedor_id') == $p->id)>
                                    {{ $p->razon_social }} {{ $p->numero_documento ? "({$p->tipo_documento}: {$p->numero_documento})" : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('proveedor_id')<small class="error">{{ $message }}</small>@enderror
                    </div>

                    <div class="campo">
                        <label>Tipo de comprobante *</label>
                        <select name="tipo_comprobante" required>
                            <option value="Factura" @selected(old('tipo_comprobante') === 'Factura')>Factura</option>
                            <option value="Boleta" @selected(old('tipo_comprobante') === 'Boleta')>Boleta</option>
                            <option value="Guía de Remisión" @selected(old('tipo_comprobante') === 'Guía de Remisión')>Guía de Remisión</option>
                            <option value="Nota de Venta" @selected(old('tipo_comprobante') === 'Nota de Venta')>Nota de Venta / Recibo</option>
                        </select>
                        @error('tipo_comprobante')<small class="error">{{ $message }}</small>@enderror
                    </div>

                    <div class="campo">
                        <label>Número de comprobante</label>
                        <input type="text" name="numero_comprobante" value="{{ old('numero_comprobante') }}" placeholder="Ej. F001-004523" maxlength="50">
                        <small class="sub">Opcional. Si lo dejas vacío se genera un código automático.</small>
                        @error('numero_comprobante')<small class="error">{{ $message }}</small>@enderror
                    </div>

                    <div class="campo">
                        <label>Fecha y hora de compra *</label>
                        <input type="datetime-local" name="fecha_compra" value="{{ old('fecha_compra', now()->format('Y-m-d\TH:i')) }}" required>
                        @error('fecha_compra')<small class="error">{{ $message }}</small>@enderror
                    </div>

                    <div class="campo full">
                        <label>Observaciones / Nota adicional</label>
                        <input type="text" name="observaciones" value="{{ old('observaciones') }}" placeholder="Detalles de entrega, forma de pago acordada, etc.">
                    </div>
                </div>

                <div class="seccion-titulo">
                    <span>2. Agregar productos a la compra</span>
                </div>

                <div class="grid-agregar">
                    <div class="campo" style="margin:0;">
                        <label>Producto *</label>
                        <select id="selProducto">
                            <option value="">-- Elige un producto --</option>
                            @foreach ($productos as $prod)
                                <option value="{{ $prod->id }}" data-nombre="{{ $prod->nombre }}" data-codigo="{{ $prod->codigo }}" data-costo="{{ $prod->precio_compra }}" data-stock="{{ $prod->stock_actual }}">
                                    [{{ $prod->codigo }}] {{ $prod->nombre }} (Stock: {{ number_format($prod->stock_actual, 0) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="campo" style="margin:0;">
                        <label>Cantidad *</label>
                        <input type="number" id="inpCantidad" min="1" step="0.001" value="1" placeholder="Cantidad">
                    </div>

                    <div class="campo" style="margin:0;">
                        <label>Costo unitario (S/) *</label>
                        <input type="number" id="inpCosto" min="0" step="0.01" value="0.00" placeholder="0.00">
                    </div>

                    <div>
                        <button class="btn btn-claro" type="button" id="btnAgregarFila" style="height:42px;">+ Agregar</button>
                    </div>
                </div>

                <div class="table-responsive" style="margin-top:10px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th style="width:130px;">Cantidad</th>
                                <th style="width:140px;">Costo unitario (S/)</th>
                                <th style="width:130px;">Subtotal (S/)</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="tablaItems">
                            <tr id="filaVacia">
                                <td colspan="5" style="text-align:center; padding:24px; color:var(--texto-3);">
                                    Aún no has agregado productos a esta compra. Selecciona uno arriba y haz clic en <strong>+ Agregar</strong>.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="totales-box">
                    <div class="totales-fila">
                        <span>Subtotal compra:</span>
                        <strong id="lblSubtotal">S/ 0.00</strong>
                    </div>
                    <div class="totales-fila" style="align-items:center;">
                        <span>Descuento global (S/):</span>
                        <input type="number" name="descuento" id="inpDescuento" step="0.01" min="0" value="{{ old('descuento', '0.00') }}" style="width:110px; padding:6px 10px; text-align:right;" oninput="calcularTotales()">
                    </div>
                    <div class="totales-fila" style="align-items:center;">
                        <span>Impuesto / IGV (S/):</span>
                        <input type="number" name="impuesto" id="inpImpuesto" step="0.01" min="0" value="{{ old('impuesto', '0.00') }}" style="width:110px; padding:6px 10px; text-align:right;" oninput="calcularTotales()">
                    </div>
                    <div class="totales-fila gran-total">
                        <span>TOTAL COMPRA:</span>
                        <span id="lblTotal">S/ 0.00</span>
                    </div>
                </div>

                <div class="form-pie" style="margin-top:30px;">
                    <a class="btn btn-claro" href="{{ route('compras.index') }}">Cancelar</a>
                    <button class="btn btn-rosa" type="submit" id="btnGuardar">Guardar y actualizar stock</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (() => {
        let items = [];
        const selProducto = document.getElementById('selProducto');
        const inpCantidad = document.getElementById('inpCantidad');
        const inpCosto    = document.getElementById('inpCosto');
        const btnAgregar  = document.getElementById('btnAgregarFila');
        const tablaItems  = document.getElementById('tablaItems');
        const filaVacia   = document.getElementById('filaVacia');
        const lblSubtotal = document.getElementById('lblSubtotal');
        const lblTotal    = document.getElementById('lblTotal');
        const inpDescuento = document.getElementById('inpDescuento');
        const inpImpuesto  = document.getElementById('inpImpuesto');
        const formCompra   = document.getElementById('formCompra');

        // Cuando se selecciona un producto, sugerir su costo
        selProducto.addEventListener('change', () => {
            const opt = selProducto.options[selProducto.selectedIndex];
            if (opt && opt.value) {
                inpCosto.value = parseFloat(opt.getAttribute('data-costo') || 0).toFixed(2);
                inpCantidad.focus();
            }
        });

        // Agregar fila
        btnAgregar.addEventListener('click', () => {
            const opt = selProducto.options[selProducto.selectedIndex];
            if (!opt || !opt.value) {
                alert('Por favor selecciona un producto');
                selProducto.focus();
                return;
            }

            const productoId = parseInt(opt.value);
            const nombre = opt.getAttribute('data-nombre');
            const codigo = opt.getAttribute('data-codigo');
            const cantidad = parseFloat(inpCantidad.value) || 0;
            const costo = parseFloat(inpCosto.value) || 0;

            if (cantidad <= 0) {
                alert('La cantidad debe ser mayor a 0');
                inpCantidad.focus();
                return;
            }
            if (costo < 0) {
                alert('El costo no puede ser negativo');
                inpCosto.focus();
                return;
            }

            // Si ya existe en la lista, sumar cantidad
            const existente = items.find(i => i.producto_id === productoId);
            if (existente) {
                existente.cantidad += cantidad;
                existente.costo = costo;
            } else {
                items.push({
                    producto_id: productoId,
                    nombre: nombre,
                    codigo: codigo,
                    cantidad: cantidad,
                    costo: costo
                });
            }

            // Limpiar inputs
            selProducto.value = '';
            inpCantidad.value = 1;
            inpCosto.value = '0.00';
            selProducto.focus();

            renderizar();
        });

        window.eliminarItem = (idx) => {
            items.splice(idx, 1);
            renderizar();
        };

        window.actualizarFila = (idx, campo, val) => {
            const num = parseFloat(val) || 0;
            if (campo === 'cantidad') items[idx].cantidad = Math.max(0.001, num);
            if (campo === 'costo') items[idx].costo = Math.max(0, num);
            renderizar();
        };

        window.calcularTotales = () => {
            let subtotal = 0;
            items.forEach(it => {
                subtotal += (it.cantidad * it.costo);
            });

            const desc = parseFloat(inpDescuento.value) || 0;
            const imp  = parseFloat(inpImpuesto.value) || 0;
            const total = Math.max(0, subtotal - desc + imp);

            lblSubtotal.innerText = 'S/ ' + subtotal.toFixed(2);
            lblTotal.innerText    = 'S/ ' + total.toFixed(2);
        };

        function renderizar() {
            tablaItems.innerHTML = '';
            if (items.length === 0) {
                tablaItems.appendChild(filaVacia);
                calcularTotales();
                return;
            }

            items.forEach((it, idx) => {
                const sub = (it.cantidad * it.costo);
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <strong>${it.nombre}</strong>
                        <span class="sub">Código: ${it.codigo}</span>
                        <input type="hidden" name="detalles[${idx}][producto_id]" value="${it.producto_id}">
                    </td>
                    <td>
                        <input type="number" step="0.001" min="0.001" style="width:100%; padding:6px 10px;" name="detalles[${idx}][cantidad]" value="${it.cantidad}" onchange="actualizarFila(${idx}, 'cantidad', this.value)">
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" style="width:100%; padding:6px 10px;" name="detalles[${idx}][costo_unitario]" value="${it.costo.toFixed(2)}" onchange="actualizarFila(${idx}, 'costo', this.value)">
                    </td>
                    <td>
                        <strong>S/ ${sub.toFixed(2)}</strong>
                    </td>
                    <td style="text-align:right;">
                        <button type="button" class="btn btn-rojo btn-sm" onclick="eliminarItem(${idx})" title="Quitar de la compra">×</button>
                    </td>
                `;
                tablaItems.appendChild(tr);
            });

            calcularTotales();
        }

        formCompra.addEventListener('submit', (e) => {
            if (items.length === 0) {
                e.preventDefault();
                alert('Debes agregar al menos un producto a la compra.');
            }
        });
    })();
</script>
@endpush
