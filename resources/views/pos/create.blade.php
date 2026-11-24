@extends('layouts.panel')

@section('titulo', __('messages.pos_title'))
@section('encabezado', __('messages.pos_header'))
@section('subtitulo', __('messages.pos_subtitle'))

@push('styles')
    @include('partials.ui')
    <style>
        .pos-container { display: grid; grid-template-columns: 1.35fr 1fr; gap: 20px; align-items: start; }
        .catalogo-box { display: flex; flex-direction: column; gap: 14px; }
        .productos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px; max-height: 580px; overflow-y: auto; padding-right: 4px; }
        .prod-card {
            background: #fff; border: 1px solid var(--borde); border-radius: 12px; padding: 12px;
            cursor: pointer; transition: all .15s ease; display: flex; flex-direction: column; justify-content: space-between;
        }
        .prod-card:hover { border-color: var(--rosa); box-shadow: 0 4px 12px rgba(214, 90, 122, .15); transform: translateY(-2px); }
        .prod-codigo { font-size: 11px; color: var(--texto-3); font-weight: 600; }
        .prod-nombre { font-size: 13.5px; font-weight: 600; color: var(--texto); margin: 6px 0 10px; line-height: 1.3; }
        .prod-pie { display: flex; justify-content: space-between; align-items: flex-end; }
        .prod-precio { font-size: 16px; font-weight: 700; color: var(--rosa-oscuro); }
        .prod-stock { font-size: 11px; color: var(--texto-2); }
        .prod-stock.bajo { color: #b91c1c; font-weight: 700; }

        /* Panel Carrito */
        .carrito-card { background: #fff; border: 1px solid var(--borde); border-radius: 14px; box-shadow: var(--sombra); padding: 20px; display: flex; flex-direction: column; gap: 16px; }
        .carrito-tabla { max-height: 300px; overflow-y: auto; margin: 8px 0; }
        .fila-item { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 8px; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--borde); font-size: 13px; }
        .totales-area { background: var(--fondo); border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 8px; }
        .tot-fila { display: flex; justify-content: space-between; font-size: 14px; }
        .tot-fila.gran-total { font-size: 20px; font-weight: 800; color: var(--rosa-oscuro); border-top: 2px dashed var(--rosa-borde); padding-top: 10px; margin-top: 4px; }
        .btn-cobrar {
            width: 100%; padding: 14px; background: linear-gradient(135deg, #e8728f, #c64a6b); color: #fff;
            border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer;
            box-shadow: 0 4px 14px rgba(214, 90, 122, .35); transition: opacity .15s;
        }
        .btn-cobrar:hover { opacity: .92; }
        @media (max-width: 950px) { .pos-container { grid-template-columns: 1fr; } }
    </style>
@endpush

@section('contenido')
    <div class="pos-container">
        <!-- COLUMNA IZQUIERDA: Catálogo de Productos y Buscador Rápido -->
        <div class="catalogo-box">
            <div class="card" style="padding:16px;">
                <input type="text" id="buscadorPos" placeholder="🔍 {{ __('messages.search_pos_placeholder') }}" autofocus style="width:100%; padding:12px 14px; font-size:14px; border:2px solid var(--borde); border-radius:10px; outline:none;">
            </div>

            <div class="productos-grid" id="gridProductos">
                @foreach ($productos as $p)
                    <div class="prod-card" data-id="{{ $p->id }}" data-nombre="{{ $p->nombre }}" data-codigo="{{ $p->codigo }}" data-barra="{{ $p->codigo_barras }}" data-precio="{{ $p->precio_venta }}" data-stock="{{ $p->stock_actual }}" data-decimales="{{ $p->permite_decimales }}" data-unidad="{{ $p->unidad ?: 'UND' }}" onclick="agregarAlCarrito({{ $p->id }})">
                        <div>
                            <div class="prod-codigo">#{{ $p->codigo }} @if($p->codigo_barras) · 🏷️ {{ $p->codigo_barras }}@endif</div>
                            <div class="prod-nombre">{{ $p->nombre }}</div>
                        </div>
                        <div class="prod-pie">
                            <div class="prod-precio">S/ {{ number_format($p->precio_venta, 2) }}</div>
                            <div class="prod-stock {{ $p->stock_actual <= 3 ? 'bajo' : '' }}">
                                Stock: {{ number_format($p->stock_actual, $p->permite_decimales ? 2 : 0) }} {{ $p->unidad }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- COLUMNA DERECHA: Carrito de Venta y Cobro -->
        <div>
            <form method="POST" action="{{ route('pos.store') }}" id="formPos">
                @csrf
                <div class="carrito-card">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--borde); padding-bottom:10px;">
                        <h2 style="font-size:16px; font-weight:700;">🛒 {{ __('messages.cart_title') }}</h2>
                        <button type="button" class="btn btn-claro btn-sm" onclick="vaciarCarrito()">{{ __('messages.empty_cart') }}</button>
                    </div>

                    <!-- Datos del Cliente -->
                    <div class="campo" style="margin:0;">
                        <label style="font-size:12px; font-weight:600; color:var(--texto-2);">{{ __('messages.customer') }}</label>
                        <select name="cliente_id" id="selCliente" style="padding:8px 10px; font-size:13px;">
                            <option value="">-- {{ __('messages.general_public') }} --</option>
                            @foreach ($clientes as $c)
                                <option value="{{ $c->id }}">
                                    {{ $c->nombres }} {{ $c->apellidos }} {{ $c->numero_documento ? "({$c->tipo_documento}: {$c->numero_documento})" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Lista de Ítems en el Carrito -->
                    <div class="carrito-tabla" id="contenedorCarrito">
                        <div id="carritoVacio" style="text-align:center; padding:32px 10px; color:var(--texto-3); font-size:13px;">
                            {{ __('messages.cart_empty_help') }}
                        </div>
                    </div>

                    <!-- Método de Pago -->
                    <div class="form-grid" style="gap:10px;">
                        <div class="campo" style="margin:0;">
                            <label style="font-size:12px; font-weight:600;">{{ __('messages.payment_method') }} *</label>
                            <select name="metodo_pago_id" id="selMetodoPago" required style="padding:8px 10px; font-size:13px;">
                                @foreach ($metodosPago as $mp)
                                    <option value="{{ $mp->id }}" data-nombre="{{ strtolower($mp->nombre) }}">
                                        {{ $mp->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="campo" style="margin:0;">
                            <label style="font-size:12px; font-weight:600;" id="lblReferencia">{{ __('messages.operation_number') }}</label>
                            <input type="text" name="referencia_pago" id="inpReferencia" placeholder="{{ __('messages.optional') }}" style="padding:8px 10px; font-size:13px;">
                        </div>

                        <div class="campo full" id="cajaEfectivo" style="display:flex; gap:12px; align-items:center; background:#fef6e4; padding:10px 12px; border-radius:10px;">
                            <div style="flex:1;">
                                <label style="font-size:11.5px; font-weight:700; color:#b7791f; margin-bottom:2px;">{{ __('messages.amount_received') }}</label>
                                <input type="number" step="0.50" min="0" name="monto_recibido" id="inpMontoRecibido" placeholder="0.00" style="padding:6px 10px; font-size:14px; font-weight:700; width:100%; border:1px solid #fed7aa; border-radius:8px;">
                            </div>
                            <div style="flex:1; text-align:right;">
                                <div style="font-size:11.5px; color:#b7791f; font-weight:600;">{{ __('messages.change_estimated') }}</div>
                                <div id="lblVuelto" style="font-size:17px; font-weight:800; color:#92400e;">S/ 0.00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen de Totales -->
                    <div class="totales-area">
                        <div class="tot-fila">
                            <span style="color:var(--texto-2);">{{ __('messages.subtotal') }}:</span>
                            <span id="lblSubtotal">S/ 0.00</span>
                        </div>
                        <div class="tot-fila gran-total">
                            <span>{{ __('messages.total_to_pay') }}:</span>
                            <span id="lblTotalVenta">S/ 0.00</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-cobrar" id="btnCobrar">
                        💵 {{ __('messages.charge_and_print') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (() => {
        let carrito = [];
        const buscador = document.getElementById('buscadorPos');
        const gridProductos = document.getElementById('gridProductos');
        const productosCards = Array.from(document.querySelectorAll('.prod-card'));
        const contenedorCarrito = document.getElementById('contenedorCarrito');
        const carritoVacio = document.getElementById('carritoVacio');
        const lblSubtotal = document.getElementById('lblSubtotal');
        const lblTotalVenta = document.getElementById('lblTotalVenta');
        const selMetodo = document.getElementById('selMetodoPago');
        const cajaEfectivo = document.getElementById('cajaEfectivo');
        const inpMontoRecibido = document.getElementById('inpMontoRecibido');
        const lblVuelto = document.getElementById('lblVuelto');
        const formPos = document.getElementById('formPos');

        // Búsqueda en vivo de productos
        buscador.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase().trim();
            productosCards.forEach(card => {
                const nombre = card.getAttribute('data-nombre').toLowerCase();
                const codigo = card.getAttribute('data-codigo').toLowerCase();
                const barra  = (card.getAttribute('data-barra') || '').toLowerCase();

                if (nombre.includes(term) || codigo.includes(term) || barra.includes(term)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });

            // Si es escaneo exacto de código de barras (enter o coincidencia exacta de código)
            const exacto = productosCards.find(c => {
                const b = (c.getAttribute('data-barra') || '').toLowerCase();
                const cod = c.getAttribute('data-codigo').toLowerCase();
                return (b === term || cod === term) && term.length >= 3;
            });
            if (exacto && e.inputType === 'insertFromPaste') {
                agregarAlCarrito(parseInt(exacto.getAttribute('data-id')));
                buscador.value = '';
            }
        });

        buscador.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const visibles = productosCards.filter(c => c.style.display !== 'none');
                if (visibles.length === 1) {
                    agregarAlCarrito(parseInt(visibles[0].getAttribute('data-id')));
                    buscador.value = '';
                    buscador.dispatchEvent(new Event('input'));
                }
            }
        });

        // Alternar caja de vuelto según método de pago
        selMetodo.addEventListener('change', () => {
            const opt = selMetodo.options[selMetodo.selectedIndex];
            const nombre = (opt.getAttribute('data-nombre') || '').toLowerCase();
            if (nombre.includes('efectivo')) {
                cajaEfectivo.style.display = 'flex';
            } else {
                cajaEfectivo.style.display = 'none';
                inpMontoRecibido.value = '';
                lblVuelto.innerText = 'S/ 0.00';
            }
        });

        inpMontoRecibido.addEventListener('input', calcularTotales);

        window.agregarAlCarrito = (id) => {
            const card = productosCards.find(c => parseInt(c.getAttribute('data-id')) === id);
            if (!card) return;

            const nombre = card.getAttribute('data-nombre');
            const precio = parseFloat(card.getAttribute('data-precio')) || 0;
            const stock = parseFloat(card.getAttribute('data-stock')) || 0;
            const decimales = parseInt(card.getAttribute('data-decimales')) === 1;
            const unidad = card.getAttribute('data-unidad');

            if (stock <= 0) {
                alert(`El producto '${nombre}' no tiene stock disponible.`);
                return;
            }

            const existente = carrito.find(it => it.id === id);
            if (existente) {
                const paso = decimales ? 1 : 1;
                if (existente.cantidad + paso > stock) {
                    alert(`No puedes agregar más unidades. El stock máximo disponible es ${stock}.`);
                    return;
                }
                existente.cantidad += paso;
            } else {
                carrito.push({
                    id: id,
                    nombre: nombre,
                    precio: precio,
                    stock: stock,
                    decimales: decimales,
                    unidad: unidad,
                    cantidad: 1
                });
            }

            renderizarCarrito();
        };

        window.cambiarCantidad = (idx, valor) => {
            const item = carrito[idx];
            let val = parseFloat(valor) || 0;

            if (!item.decimales) {
                val = Math.floor(val);
            }

            if (val <= 0) {
                carrito.splice(idx, 1);
            } else if (val > item.stock) {
                alert(`Stock máximo disponible: ${item.stock}`);
                item.cantidad = item.stock;
            } else {
                item.cantidad = val;
            }

            renderizarCarrito();
        };

        window.eliminarItemCarrito = (idx) => {
            carrito.splice(idx, 1);
            renderizarCarrito();
        };

        window.vaciarCarrito = () => {
            if (carrito.length && confirm('¿Deseas vaciar todos los productos del carrito?')) {
                carrito = [];
                renderizarCarrito();
            }
        };

        function renderizarCarrito() {
            contenedorCarrito.innerHTML = '';

            if (carrito.length === 0) {
                contenedorCarrito.appendChild(carritoVacio);
                lblSubtotal.innerText = 'S/ 0.00';
                lblTotalVenta.innerText = 'S/ 0.00';
                lblVuelto.innerText = 'S/ 0.00';
                return;
            }

            carrito.forEach((it, idx) => {
                const sub = it.cantidad * it.precio;
                const div = document.createElement('div');
                div.className = 'fila-item';
                div.innerHTML = `
                    <div>
                        <strong>${it.nombre}</strong>
                        <span class="sub">S/ ${it.precio.toFixed(2)} c/u</span>
                        <input type="hidden" name="items[${idx}][producto_id]" value="${it.id}">
                    </div>
                    <div>
                        <input type="number" name="items[${idx}][cantidad]" value="${it.cantidad}" step="${it.decimales ? '0.001' : '1'}" min="${it.decimales ? '0.001' : '1'}" max="${it.stock}" style="width:100%; padding:4px 6px; font-weight:700; text-align:center;" onchange="cambiarCantidad(${idx}, this.value)">
                    </div>
                    <div style="text-align:right;">
                        <strong>S/ ${sub.toFixed(2)}</strong>
                    </div>
                    <div>
                        <button type="button" class="btn btn-rojo btn-sm" onclick="eliminarItemCarrito(${idx})" style="padding:2px 8px;">×</button>
                    </div>
                `;
                contenedorCarrito.appendChild(div);
            });

            calcularTotales();
        }

        function calcularTotales() {
            let total = 0;
            carrito.forEach(it => {
                total += (it.cantidad * it.precio);
            });

            lblSubtotal.innerText = 'S/ ' + total.toFixed(2);
            lblTotalVenta.innerText = 'S/ ' + total.toFixed(2);

            const recibido = parseFloat(inpMontoRecibido.value) || 0;
            const vuelto = (recibido > total) ? (recibido - total) : 0;
            lblVuelto.innerText = 'S/ ' + vuelto.toFixed(2);
        }

        formPos.addEventListener('submit', (e) => {
            if (carrito.length === 0) {
                e.preventDefault();
                alert('El carrito está vacío. Agrega productos para cobrar.');
            }
        });
    })();
</script>
@endpush
