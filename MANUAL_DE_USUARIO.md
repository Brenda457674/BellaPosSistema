<!-- ==============================================================================
     MANUAL DE USUARIO - SISTEMA DE PUNTO DE VENTA Y GESTIÓN BELLA FLOR POS
     ============================================================================== -->

<div align="center">

# SISTEMA DE PUNTO DE VENTA Y GESTIÓN FLORERÍA BELLA FLOR
## MANUAL DE USUARIO DEL SISTEMA

<br>

![Logotipo de Bella Flor POS](docs/img/01_caratula_logo.png)

<br>

**Versión del Software:** 1.0.0 (Release Final)  
**Entorno de Operación:** Web / Localhost / Servidor Cloud  
**Fecha de Publicación:** Noviembre 2026  

---

### **DATOS INFORMATIVOS**

| Campo | Detalle |
| :--- | :--- |
| **Institución / Organización:** | [Ingresa aquí tu Institución / Universidad / Instituto / Empresa] |
| **Carrera / Especialidad:** | [Ingresa aquí la Carrera o Área] |
| **Curso / Asignatura:** | [Ingresa aquí el Nombre del Curso o Proyecto] |
| **Proyecto:** | Sistema Web de Punto de Venta y Gestión de Inventario "Bella Flor POS" |
| **Integrantes / Autores:** | 1. [Nombre del Integrante 1]<br>2. [Nombre del Integrante 2]<br>3. [Nombre del Integrante 3] |
| **Docente / Asesor:** | [Nombre del Docente o Supervisor] |
| **Lugar y Fecha:** | 2026 |

---

</div>

<div style="page-break-after: always;"></div>

## FICHA TÉCNICA DEL SISTEMA

* **Nombre de la Aplicación:** Bella Flor POS
* **Tipo de Aplicación:** Sistema Web Responsivo (Punto de Venta e Inventario)
* **Arquitectura:** Modelo - Vista - Controlador (MVC) sobre Laravel Framework
* **Base de Datos:** MySQL / MariaDB (vía phpMyAdmin / XAMPP)
* **Lenguajes y Tecnologías:** PHP 8+, JavaScript (ES6+), HTML5, CSS3, Blade Templating
* **Moneda de Transacción:** Soles Peruanos (S/)
* **Idiomas Soportados:** Español (predeterminado), Inglés, Italiano

---

## ÍNDICE GENERAL

1. [CAPÍTULO 1: INTRODUCCIÓN Y ASPECTOS GENERALES](#capítulo-1-introducción-y-aspectos-generales)
   * 1.1. Objetivo del Manual
   * 1.2. Alcance y Funcionalidades del Sistema
   * 1.3. Requisitos de Hardware y Software
   * 1.4. Perfiles y Matriz de Roles de Usuario
2. [CAPÍTULO 2: ACCESO, SEGURIDAD Y CONFIGURACIÓN PERSONAL](#capítulo-2-acceso-seguridad-y-configuración-personal)
   * 2.1. Vista Pública del Catálogo (Para Visitantes)
   * 2.2. Inicio de Sesión (Login)
   * 2.3. Recuperación y Restablecimiento de Contraseña
   * 2.4. Cambio de Idioma del Sistema
   * 2.5. Gestión de Perfil de Usuario y Cambio de Clave
   * 2.6. Cierre de Sesión Seguro
3. [CAPÍTULO 3: PANELES PRINCIPALES (DASHBOARDS)](#capítulo-3-paneles-principales-dashboards)
   * 3.1. Dashboard del Administrador
   * 3.2. Panel de Control del Vendedor
   * 3.3. Panel de Control del Almacenero
4. [CAPÍTULO 4: MÓDULO DE PUNTO DE VENTA (POS) Y VENTAS](#capítulo-4-módulo-de-punto-de-venta-pos-y-ventas)
   * 4.1. Interfaz de Venta Rápida
   * 4.2. Búsqueda y Selección de Productos
   * 4.3. Manejo del Carrito de Compras y Cantidades
   * 4.4. Asignación de Cliente (Público General o Registrado)
   * 4.5. Selección de Método de Pago y Registro del Vuelto
   * 4.6. Emisión e Impresión del Comprobante (Ticket de Venta)
   * 4.7. Consulta de Historial y Anulación de Ventas
5. [CAPÍTULO 5: MÓDULO DE PRODUCTOS, CATEGORÍAS E INVENTARIO](#capítulo-5-módulo-de-productos-categorías-e-inventario)
   * 5.1. Catálogo General de Productos
   * 5.2. Registro de Nuevos Productos
   * 5.3. Modificación y Desactivación de Productos
   * 5.4. Gestión de Categorías
   * 5.5. Gestión de Unidades de Medida
   * 5.6. Control de Inventario y Kardex de Movimientos
   * 5.7. Registro de Ajustes Manuales de Stock
6. [CAPÍTULO 6: MÓDULO DE COMPRAS Y PROVEEDORES](#capítulo-6-módulo-de-compras-y-proveedores)
   * 6.1. Directorio y Registro de Proveedores
   * 6.2. Registro de Nueva Compra / Ingreso de Mercadería
   * 6.3. Historial de Compras y Detalle de Ingresos
   * 6.4. Anulación de Compras
7. [CAPÍTULO 7: MÓDULO DE CLIENTES](#capítulo-7-módulo-de-clientes)
   * 7.1. Listado y Búsqueda de Clientes
   * 7.2. Registro y Actualización de Datos de Clientes
   * 7.3. Historial de Consumo del Cliente
8. [CAPÍTULO 8: MÓDULO DE REPORTES Y ESTADÍSTICAS](#capítulo-8-módulo-de-reportes-y-estadísticas)
   * 8.1. Consulta de Reportes de Ventas
   * 8.2. Reporte de Productos Más Vendidos
   * 8.3. Filtros por Fechas y Exportación
9. [CAPÍTULO 9: MÓDULO DE ADMINISTRACIÓN Y CONFIGURACIÓN](#capítulo-9-módulo-de-administración-y-configuración)
   * 9.1. Gestión de Usuarios y Asignación de Roles
   * 9.2. Configuración General de la Tienda (Datos para Tickets)
10. [CAPÍTULO 10: PREGUNTAS FRECUENTES Y SOLUCIÓN DE PROBLEMAS (FAQ)](#capítulo-10-preguntas-frecuentes-y-solución-de-problemas-faq)

---

<div style="page-break-after: always;"></div>

# CAPÍTULO 1: INTRODUCCIÓN Y ASPECTOS GENERALES

## 1.1. Objetivo del Manual
El presente **Manual de Usuario** tiene como finalidad brindar una guía detallada, visual y paso a paso para la correcta operación, gestión y administración del sistema web **Bella Flor POS**, optimizado para el control comercial, inventario y facturación ágil de florerías y negocios afines.

## 1.2. Alcance y Funcionalidades del Sistema
**Bella Flor POS** automatiza el ciclo comercial completo de la tienda:
* **Gestión de Ventas:** Punto de venta ágil compatible con lectores de códigos de barras, múltiples medios de pago y emisión inmediata de comprobantes.
* **Control de Stock en Tiempo Real:** Seguimiento de entradas, salidas, kardex detallado y alertas automáticas de productos próximos a agotarse.
* **Cadena de Suministro:** Registro de proveedores, compras y actualización inmediata del inventario físico.
* **Gestión de Clientes:** Directorio centralizado de clientes con trazabilidad de compras y comprobantes emitidos.
* **Métricas y Análisis:** Dashboards analíticos con gráficos en tiempo real y módulo de reportes periódicos.
* **Multiusuario y Multirrol:** Control estricto de acceso y permisos según el puesto del personal.

## 1.3. Requisitos de Hardware y Software

### Requisitos del Cliente (Dispositivo de Trabajo)
* **Dispositivo:** Computadora de escritorio, laptop o tablet.
* **Sistema Operativo:** Windows 10/11, macOS, Linux, o Android.
* **Navegador Web Recomendado:** Google Chrome (versión 100+), Mozilla Firefox, Microsoft Edge o Safari.
* **Periféricos Opcionales:**
  * Lector óptico de código de barras USB o Bluetooth.
  * Impresora térmica para tickets de 58mm u 80mm.
  * Gaveta de dinero conectada a la impresora térmica.

### Requisitos del Servidor (Para despliegue o ejecución local)
* Servidor Web Apache / Nginx.
* PHP versión 8.1 o superior con extensiones PDO, OpenSSL, Mbstring y GD activadas.
* Base de datos MySQL / MariaDB versión 5.7 o 10.3+.

## 1.4. Perfiles y Matriz de Roles de Usuario
El sistema opera bajo un esquema de control de acceso basado en roles (**RBAC**):

| Módulo / Función | Administrador | Vendedor / Cajero | Almacenero |
| :--- | :---: | :---: | :---: |
| **Vista Pública del Catálogo** | ✔️ Sí | ✔️ Sí | ✔️ Sí |
| **Acceso a Dashboard General de Métricas** | ✔️ Total | ❌ No | ❌ No |
| **Panel de Control Personalizado** | ✔️ Sí | ✔️ Ventas | ✔️ Almacén |
| **Punto de Venta (Registrar Ventas y Cobros)** | ✔️ Sí | ✔️ Sí | ❌ No |
| **Anular Ventas Realizadas** | ✔️ Sí (Exclusivo) | ❌ No | ❌ No |
| **Consultar Catálogo de Productos** | ✔️ Sí | ✔️ Sí | ✔️ Sí |
| **Crear y Modificar Productos** | ✔️ Sí | ❌ No | ✔️ Sí |
| **Eliminar / Desactivar Productos** | ✔️ Sí (Exclusivo) | ❌ No | ❌ No |
| **Gestión de Categorías y Unidades** | ✔️ Total | ❌ No | ✔️ Registro/Edición |
| **Kardex y Ajuste Manual de Stock** | ✔️ Sí | ❌ No | ✔️ Sí |
| **Registro de Compras a Proveedores** | ✔️ Sí | ❌ No | ✔️ Sí |
| **Anular Compras a Proveedores** | ✔️ Sí (Exclusivo) | ❌ No | ❌ No |
| **Gestión de Proveedores** | ✔️ Total | ❌ No | ✔️ Registro/Edición |
| **Gestión de Clientes** | ✔️ Total | ✔️ Registro/Edición | ❌ No |
| **Eliminar Clientes** | ✔️ Sí (Exclusivo) | ❌ No | ❌ No |
| **Reportes y Estadísticas Avanzadas** | ✔️ Total | ❌ No | ❌ No |
| **Gestión de Usuarios y Roles** | ✔️ Sí (Exclusivo) | ❌ No | ❌ No |
| **Configuración General de la Tienda** | ✔️ Sí (Exclusivo) | ❌ No | ❌ No |
| **Modificar Perfil y Contraseña Propia** | ✔️ Sí | ✔️ Sí | ✔️ Sí |

---

<div style="page-break-after: always;"></div>

# CAPÍTULO 2: ACCESO, SEGURIDAD Y CONFIGURACIÓN PERSONAL

## 2.1. Vista Pública del Catálogo (Para Visitantes)
Cualquier cliente o visitante puede ingresar a la dirección web pública del sistema sin necesidad de registrarse. Desde aquí puede explorar las flores, ramos y arreglos disponibles, con sus descripciones y precios actualizados.

![Vista Pública del Catálogo](docs/img/02_vista_publica.png)
*Figura 2.1: Pantalla de inicio y catálogo público de Bella Flor.*

**Pasos para navegar el catálogo:**
1. Ingrese a la URL principal: `http://localhost/BellaFlorPOS/public/publico` (o dominio del negocio).
2. Use la barra de búsqueda o filtre por categoría floral (Ramos, Arreglos, Rosas, Ocasiones).
3. Haga clic sobre cualquier producto para visualizar sus especificaciones y precio al público.

---

## 2.2. Inicio de Sesión (Login)
Para acceder a las funciones administrativas o comerciales es indispensable autenticarse con credenciales válidas.

![Inicio de Sesión](docs/img/03_login.png)
*Figura 2.2: Formulario de autenticación del sistema.*

**Procedimiento:**
1. Diríjase a la ruta `/login` o haga clic en el botón superior **"Iniciar Sesión"**.
2. Ingrese su **Correo Electrónico** registrado.
3. Ingrese su **Contraseña** secreta.
4. (Opcional) Marque la casilla **"Recordarme"** si utiliza un equipo personal y de confianza.
5. Presione el botón **"Ingresar al Sistema"**.
6. El sistema validará su rol y lo redirigirá automáticamente a su panel de trabajo correspondiente.

> [!NOTE]
> Si ingresa credenciales erróneas más de 5 veces seguidas, el sistema aplicará un bloqueo temporal de seguridad para proteger la cuenta.

---

## 2.3. Recuperación y Restablecimiento de Contraseña
Si un usuario olvida su contraseña de acceso, puede restablecerla mediante correo electrónico.

![Recuperación de Contraseña](docs/img/04_recuperar_password.png)
*Figura 2.3: Ventana de recuperación de contraseña.*

**Procedimiento:**
1. En la pantalla de login, presione el enlace **"¿Olvidaste tu contraseña?"**.
2. Escriba el correo electrónico con el que está registrado en el sistema.
3. Presione el botón **"Enviar enlace de restablecimiento"**.
4. Revise su bandeja de entrada (o carpeta de spam) y abra el correo recibido.
5. Ingrese al enlace proporcionado, defina su nueva contraseña y confírmela.

---

## 2.4. Cambio de Idioma del Sistema
Bella Flor POS cuenta con soporte multilingüe disponible en la barra superior o en el pie de página.

**Procedimiento:**
1. Ubique el selector de idioma en la barra de navegación.
2. Seleccione el idioma de su preferencia:
   * 🇪🇸 **Español**
   * 🇬🇧 **English**
   * 🇮🇹 **Italiano**
3. El sistema recargará la vista adaptando etiquetas, botones y mensajes del sistema de manera instantánea.

---

## 2.5. Gestión de Perfil de Usuario y Cambio de Clave
Cualquier usuario autenticado puede actualizar sus datos informativos y cambiar su clave de acceso periódicamente.

![Perfil del Usuario](docs/img/05_perfil_usuario.png)
*Figura 2.4: Formulario de edición del perfil de usuario.*

**Procedimiento para actualizar información:**
1. Haga clic sobre su nombre de usuario en la esquina superior derecha y seleccione **"Mi Perfil"** (`/perfil`).
2. Modifique su nombre completo o teléfono de contacto.
3. Presione **"Guardar Cambios"**.

**Procedimiento para cambiar contraseña:**
1. En la misma pantalla, diríjase a la sección **"Seguridad / Cambiar Contraseña"**.
2. Ingrese su **Contraseña Actual**.
3. Ingrese su **Nueva Contraseña** (mínimo 8 caracteres, combinando números y letras).
4. Confirme la nueva contraseña y presione **"Actualizar Contraseña"**.

---

## 2.6. Cierre de Sesión Seguro
Al finalizar su turno o abandonar el equipo de cómputo, es obligatorio cerrar la sesión para proteger la integridad de las ventas y la caja:
1. Haga clic en su nombre o foto de perfil en el menú superior.
2. Presione la opción **"Cerrar Sesión"**.
3. El sistema destruirá la sesión activa y volverá a la pantalla de login.

---

<div style="page-break-after: always;"></div>

# CAPÍTULO 3: PANELES PRINCIPALES (DASHBOARDS)

## 3.1. Dashboard del Administrador
Es el centro de control estratégico del negocio. Ofrece un panorama global de la florería en tiempo real.

![Dashboard del Administrador](docs/img/06_dashboard_admin.png)
*Figura 3.1: Panel de control gerencial del Administrador.*

### Componentes del Dashboard:
* **Tarjetas de Resumen (KPIs):**
  * **Ventas del Día:** Monto total facturado en la jornada actual en Soles (S/).
  * **Ventas del Mes:** Ingresos acumulados durante el mes en curso.
  * **Total de Clientes Activos:** Número de clientes registrados en base de datos.
  * **Alertas de Stock Bajo:** Cantidad de productos florales en nivel crítico de inventario.
* **Gráfica de Ventas Semanal / Mensual:** Visualización interactiva de la tendencia de ingresos.
* **Top de Flores y Productos Más Vendidos:** Clasificación de los productos con mayor rotación.
* **Accesos Directos:** Botones para ir directamente al POS, crear nuevo producto o registrar una compra.

---

## 3.2. Panel de Control del Vendedor
Diseñado para la atención rápida al cliente, facilitando los atajos operativos del personal en caja.

![Dashboard del Vendedor](docs/img/07_dashboard_vendedor.png)
*Figura 3.2: Panel de trabajo del Vendedor.*

### Componentes:
* Acceso directo en un solo clic a **"Nueva Venta (POS)"**.
* Lista de ventas emitidas por el cajero durante el día.
* Resumen de total cobrado en la caja personal por tipo de medio de pago (Efectivo vs. Medios Digitales).
* Acceso rápido al catálogo de productos y búsqueda de clientes.

---

## 3.3. Panel de Control del Almacenero
Orientado exclusivamente a la logística, control de existencias y reabastecimiento.

![Dashboard del Almacenero](docs/img/08_dashboard_almacenero.png)
*Figura 3.3: Panel de control de almacén e inventario.*

### Componentes:
* Lista prioritaria de **Productos con Stock Crítico** (requieren compra urgente).
* Atajo directo a **"Registrar Nueva Compra a Proveedor"**.
* Enlace rápido a **"Ajustes de Inventario"** y **"Kardex Físico"**.
* Indicador de total de ítems almacenados y movimientos registrados en la semana.

---

<div style="page-break-after: always;"></div>

# CAPÍTULO 4: MÓDULO DE PUNTO DE VENTA (POS) Y VENTAS

## 4.1. Interfaz de Venta Rápida
El módulo POS (`/pos/nueva-venta`) está diseñado con una estructura de doble columna para maximizar la velocidad de atención al cliente en mostrador:
* **Columna Izquierda:** Buscador inteligente y cuadrícula interactiva de productos disponibles con stock en vivo.
* **Columna Derecha:** Panel del carrito de compras, selección de cliente, resumen de montos y botón de cobro.

![Interfaz Principal del POS](docs/img/09_pos_interfaz.png)
*Figura 4.1: Módulo interactivo de Punto de Venta.*

---

## 4.2. Búsqueda y Selección de Productos
El sistema ofrece tres métodos ultra rápidos para añadir productos a la venta:

1. **Por Lector de Código de Barras (Escáner):**
   * Con el cursor en el buscador de la pantalla, simplemente escanee el código de barras adherido al producto o ramo.
   * El producto se añadirá automáticamente al carrito sin necesidad de tocar el teclado ni el ratón.
2. **Por Nombre o Código Manual:**
   * Escriba las primeras letras del producto (ejemplo: `Rosa`, `Girasol`, `Orquídea`) o su código interno `#001`.
   * El catálogo filtrará instantáneamente los resultados.
3. **Por Clic Directo en la Cuadrícula:**
   * Haga clic sobre la tarjeta del producto deseado en la pantalla táctil o con el ratón.

> [!WARNING]
> Si un producto se encuentra con **Stock en 0**, la tarjeta aparecerá deshabilitada o en color de advertencia impidiendo la venta en negativo.

---

## 4.3. Manejo del Carrito de Compras y Cantidades
Una vez agregado el producto al carrito:
* **Aumentar o Disminuir Cantidad:** Utilice los botones `+` y `-` o escriba directamente la cantidad deseada.
* **Productos por Fracción o Unidad:** Aquellos productos configurados con unidades como docenas, ramos o kilos permiten el ingreso de decimales si fue habilitado en su registro.
* **Precio Unitario y Subtotal:** Se calculan de forma instantánea al modificar cualquier cantidad.
* **Eliminar Producto:** Presione el botón de papelera o la `X` roja junto al ítem para removerlo de la lista.

---

## 4.4. Asignación de Cliente
En la parte superior del carrito de compras:
* Por defecto, la venta se asigna a **"Público General / Cliente Varios"**.
* Si el cliente desea comprobante personalizado (Boleta o Factura con datos):
  1. Escriba el nombre, DNI o RUC en el selector de cliente.
  2. Si el cliente no existe, presione el botón **`+` (Nuevo Cliente)** para registrarlo sin salir del POS.

---

## 4.5. Selección de Método de Pago y Registro del Vuelto
Al presionar el botón **"Cobrar / Finalizar Venta"**:

![Cobro y Medios de Pago](docs/img/10_pos_carrito_cobro.png)
*Figura 4.2: Modal o panel de selección de forma de pago y cálculo de cambio.*

1. **Seleccione el Método de Pago:**
   * 💵 **Efectivo:** Ingrese el monto entregado por el cliente en el campo *"Monto Recibido"*. El sistema calculará el **Vuelto** exacto en pantalla.
   * 💳 **Tarjeta (Débito / Crédito):** Ingrese el número de operación o referencia POS.
   * 📱 **Billetera Digital (Yape / Plin / Transferencia):** Ingrese el código de confirmación del pago móvil.
2. Verifique que el total cuadre y presione **"Confirmar y Emitir Venta"**.

---

## 4.6. Emisión e Impresión del Comprobante (Ticket de Venta)
Al registrar la venta, el sistema genera la vista del comprobante (`/pos/{id}`):

![Ticket de Venta Generado](docs/img/11_pos_ticket_venta.png)
*Figura 4.3: Vista previa e impresión del ticket de venta.*

* **Datos que incluye el Ticket:**
  * Logotipo, Nombre Comercial y RUC de Bella Flor.
  * Dirección física, número de teléfono y mensaje de agradecimiento.
  * Número correlativo de Comprobante / Ticket.
  * Fecha y hora exacta de la transacción.
  * Nombre del cajero/vendedor responsable.
  * Detalle de productos, cantidades, precios unitarios y subtotal.
  * Método de pago empleado y vuelto entregado.
* **Impresión:** Presione el botón **"Imprimir Ticket"** (compatible con impresoras térmicas de 80mm y 58mm) o presione `Ctrl + P`.

---

## 4.7. Consulta de Historial y Anulación de Ventas
En el menú **POS 👉 Listado de Ventas** (`/pos`):

![Historial de Ventas](docs/img/12_pos_historial_ventas.png)
*Figura 4.4: Listado histórico de ventas realizadas.*

* **Filtros:** Puede buscar por número de comprobante, rango de fechas, cliente o vendedor.
* **Ver Comprobante:** Permite reimprimir cualquier ticket en cualquier momento.
* **Anulación de Venta (Solo Administrador):**
  1. Si un cliente devolvió el pedido o hubo una equivocación en el cobro, el Administrador ubica la venta y presiona **"Anular"**.
  2. El sistema solicita confirmación del motivo.
  3. Al anularse, **el stock de los productos involucrados se reintegra automáticamente al inventario** y el estado de la venta pasa a `ANULADA`.

---

<div style="page-break-after: always;"></div>

# CAPÍTULO 5: MÓDULO DE PRODUCTOS, CATEGORÍAS E INVENTARIO

## 5.1. Catálogo General de Productos
Ubicado en el menú **Productos** (`/productos`), permite auditar la totalidad de flores, insumos y accesorios registrados.

![Catálogo de Productos](docs/img/13_productos_listado.png)
*Figura 5.1: Tabla de administración de productos con búsqueda y filtros.*

* **Información visible:** Código, Imagen en miniatura, Nombre, Categoría, Unidad de Medida, Precio de Costo, Precio de Venta, Stock Actual y Estado (Activo/Inactivo).
* **Filtros rápidos:** Permite filtrar por categoría floral, productos por acabarse o buscar por texto libre.

---

## 5.2. Registro de Nuevos Productos
Para añadir un nuevo producto o arreglo floral al catálogo:

![Creación de Producto](docs/img/14_productos_crear.png)
*Figura 5.2: Formulario de alta de nuevo producto.*

**Campos a completar:**
1. **Código del Producto:** Código interno identificador (ejemplo: `FL-001`).
2. **Código de Barras:** Código para lectura con escáner óptico (si no tiene, puede generarse o dejarse vacío).
3. **Nombre del Producto:** Nombre descriptivo claro (ejemplo: *"Ramo de 12 Rosas Rojas Premium"*).
4. **Categoría:** Seleccione la categoría correspondiente (ejemplo: Rosas, Arreglos Fúnebres, Cumpleaños).
5. **Unidad de Medida:** Unidad (UND), Docena (DOC), Ramo (RMO), etc.
6. **Precio de Compra / Costo (S/):** Costo que cobra el proveedor por unidad.
7. **Precio de Venta al Público (S/):** Precio final que se cobrará al cliente en mostrador.
8. **Stock Mínimo:** Nivel umbral que activará las alertas automáticas de reposición (ejemplo: 5 unidades).
9. **Permite Decimales:** Activar si el producto se vende por peso o fracción.
10. **Imagen del Producto:** Suba una fotografía en formato JPG o PNG para que aparezca en el POS y en la vista pública.
11. Presione **"Guardar Producto"**.

---

## 5.3. Modificación y Desactivación de Productos
* **Edición (`/productos/{id}/edit`):** Permite cambiar precios, nombres, categoría, imagen o stock mínimo. Los cambios se reflejan inmediatamente en el POS.
* **Desactivación / Eliminación Lógica:** Si un producto deja de comercializarse temporalmente, el Administrador puede desactivarlo para que no aparezca en el POS sin perder el historial de ventas pasadas.

---

## 5.4. Gestión de Categorías
Ubicado en **Categorías** (`/categorias`). Permite clasificar el inventario para facilitar la búsqueda en el catálogo y en los reportes de ventas.

![Gestión de Categorías](docs/img/15_categorias_listado.png)
*Figura 5.3: Mantenimiento de categorías de productos.*

* **Crear Categoría:** Ingrese el nombre (ejemplo: *"Rosas Importadas"*, *"Girasoles"*, *"Peluches y Globos"*), una breve descripción y el estado (Activo).
* **Editar:** Permite renombrar o actualizar la información de la categoría.

---

## 5.5. Gestión de Unidades de Medida
Ubicado en **Unidades de Medida** (`/unidades`).

![Unidades de Medida](docs/img/16_unidades_medida.png)
*Figura 5.4: Módulo de configuración de unidades de medida.*

* Permite registrar cómo se cuantifica cada artículo:
  * `UND` - Unidad individual.
  * `DOC` - Docena.
  * `PAQ` - Paquete / Manojo.
  * `KG` - Kilogramo.
* Define si la unidad admite ventas fraccionadas con decimales o únicamente enteros.

---

## 5.6. Control de Inventario y Kardex de Movimientos
En **Inventario 👉 Kardex** (`/inventario/kardex`):

![Kardex de Inventario](docs/img/17_inventario_kardex.png)
*Figura 5.5: Auditoría de movimientos del Kardex físico.*

* Permite ver la trazabilidad cronológica de cada flor o artículo:
  * **Fecha y Hora del movimiento.**
  * **Tipo:** Entrada (por compra a proveedor), Salida (por venta en POS), Ajuste positivo o Ajuste negativo.
  * **Comprobante Asociado:** Número de ticket de venta o factura de compra.
  * **Cantidad y Saldo Final:** Existencia previa y stock resultante tras la operación.
  * **Usuario Responsable:** Identificador del empleado que realizó la transacción.

---

## 5.7. Registro de Ajustes Manuales de Stock
En **Inventario 👉 Ajuste de Stock** (`/inventario/ajuste`):

![Ajuste de Stock](docs/img/18_inventario_ajuste.png)
*Figura 5.6: Formulario de ajuste físico de inventario.*

Se utiliza cuando el stock real físico no coincide con el sistema (por ejemplo, mermas de flores marchitas, roturas en transporte o sobrantes tras conteo físico):
1. Seleccione el **Producto**.
2. Elija el **Tipo de Ajuste**:
   * **Entrada (+):** Suma existencias al inventario.
   * **Salida (-):** Descuenta existencias del inventario.
3. Ingrese la **Cantidad a ajustar**.
4. Detalle el **Motivo / Justificación** (obligatorio para auditorías, ejemplo: *"3 rosas marchitas descartadas"*).
5. Presione **"Aplicar Ajuste"**.

---

<div style="page-break-after: always;"></div>

# CAPÍTULO 6: MÓDULO DE COMPRAS Y PROVEEDORES

## 6.1. Directorio y Registro de Proveedores
En el menú **Proveedores** (`/proveedores`), se administra a los agricultores, mayoristas y distribuidores de insumos.

![Directorio de Proveedores](docs/img/19_proveedores_listado.png)
*Figura 6.1: Lista y mantenimiento de proveedores registrados.*

**Para registrar un nuevo proveedor:**
1. Presione **"Nuevo Proveedor"** (`/proveedores/create`).
2. Ingrese la **Razón Social** o Nombre del mayorista.
3. Ingrese el número de **RUC / Documento de Identidad**.
4. Ingrese **Teléfono de Contacto**, **Correo Electrónico** y **Dirección**.
5. Presione **"Guardar Proveedor"**.

---

## 6.2. Registro de Nueva Compra / Ingreso de Mercadería
En **Compras 👉 Nueva Compra** (`/compras/create`):

![Registro de Compra](docs/img/20_compras_nueva.png)
*Figura 6.2: Registro de compra y entrada de inventario.*

**Pasos para ingresar mercadería:**
1. Seleccione el **Proveedor** que abastece la orden.
2. Seleccione el **Tipo de Comprobante** (Factura, Boleta de compra o Guía de Remisión) e ingrese su número de serie/número correlativo.
3. Indique la **Fecha de Compra**.
4. En la sección de detalle, seleccione el producto, ingrese la **Cantidad comprada** y el **Precio de Costo Unitario**.
5. Si compró varios productos, presione **"+ Añadir Fila"** e ingrese los demás ítems.
6. El sistema calculará el total global de la compra en Soles.
7. Presione **"Procesar Compra"**.

> [!IMPORTANT]
> Al procesar la compra, el sistema **incrementa de forma inmediata el stock actual** de cada producto en el inventario y genera el registro en el Kardex.

---

## 6.3. Historial de Compras y Detalle de Ingresos
En **Compras 👉 Listado** (`/compras`):
* Muestra el resumen de todas las compras realizadas: proveedor, fecha, total pagado y usuario que la registró.
* Al hacer clic en **"Ver Detalle"** (`/compras/{id}`), se despliega la orden completa con la relación de ítems y costos.

![Historial de Compras](docs/img/21_compras_historial.png)
*Figura 6.3: Historial y consulta del detalle de compras.*

---

## 6.4. Anulación de Compras
Si una compra se registró por error, el **Administrador** puede anularla desde la vista de detalle:
* El sistema pedirá confirmación.
* Al anularla, **se descuenta automáticamente del inventario el stock que había ingresado**.

---

<div style="page-break-after: always;"></div>

# CAPÍTULO 7: MÓDULO DE CLIENTES

## 7.1. Listado y Búsqueda de Clientes
En el menú **Clientes** (`/clientes`), se gestiona el directorio de compradores frecuentes, empresas o clientes particulares.

![Listado de Clientes](docs/img/22_clientes_listado.png)
*Figura 7.1: Base de datos centralizada de clientes.*

* Permite buscar por nombres, apellidos, DNI, RUC o teléfono móvil.
* Muestra el total de ventas asociadas a cada cliente.

---

## 7.2. Registro y Actualización de Datos de Clientes
**Procedimiento para registrar:**
1. Haga clic en **"Nuevo Cliente"** (`/clientes/create`).
2. Seleccione el **Tipo de Documento** (DNI, RUC, Carné de Extranjería, Pasaporte).
3. Escriba el **Número de Documento**.
4. Ingrese los **Nombres / Razón Social**.
5. Ingrese **Teléfono / WhatsApp**, **Correo Electrónico** y **Dirección de Envío**.
6. Presione **"Guardar Cliente"**.

---

## 7.3. Historial de Consumo del Cliente
Al ingresar al perfil del cliente registrado, es posible revisar todas las compras que ha realizado en la florería a lo largo del tiempo, facilitando la fidelización, atención personalizada de aniversarios o fechas especiales.

---

<div style="page-break-after: always;"></div>

# CAPÍTULO 8: MÓDULO DE REPORTES Y ESTADÍSTICAS

## 8.1. Consulta de Reportes de Ventas
Disponible exclusivamente para el rol de **Administrador** en la ruta `/reportes`:

![Reportes y Estadísticas](docs/img/23_reportes_ventas.png)
*Figura 8.1: Módulo analítico y reportes financieros.*

* **Filtros Disponibles:**
  * **Rango de Fechas:** Diario, Semanal, Mensual o Rango Personalizado (Fecha Inicial y Fecha Final).
  * **Por Cajero / Vendedor:** Permite evaluar el desempeño individual del personal.
  * **Por Método de Pago:** Total recaudado en Efectivo, Tarjeta, Yape o Plin.
* **Resumen de Resultados:** Muestra el total de operaciones, monto bruto, descuentos aplicados y ganancia neta estimada.

---

## 8.2. Reporte de Productos Más Vendidos
Presenta una tabla analítica con los productos estrella de la florería:
* Nombre de la flor o arreglo.
* Cantidad total de unidades vendidas.
* Ingreso total recaudado por cada ítem.
* Porcentaje de aporte a las ventas totales de la tienda.

---

## 8.3. Filtros por Fechas y Exportación
* Los resultados pueden imprimirse directamente con formato optimizado para gerencia o exportarse para análisis en hojas de cálculo externas.

---

<div style="page-break-after: always;"></div>

# CAPÍTULO 9: MÓDULO DE ADMINISTRACIÓN Y CONFIGURACIÓN

## 9.1. Gestión de Usuarios y Asignación de Roles
Ubicado en **Usuarios** (`/usuarios`), permite al Administrador controlar quién tiene acceso al sistema.

![Gestión de Usuarios](docs/img/24_usuarios_listado.png)
*Figura 9.1: Directorio de usuarios y asignación de permisos.*

### Procedimiento para crear un nuevo usuario:
1. Haga clic en **"Nuevo Usuario"** (`/usuarios/create`).
2. Ingrese el **Nombre Completo** del empleado.
3. Ingrese su **Correo Electrónico** (servirá como usuario de acceso).
4. Asigne una **Contraseña Provisional**.
5. Seleccione el **Rol**:
   * **Administrador:** Acceso ilimitado.
   * **Vendedor:** Acceso únicamente a caja, ventas POS y clientes.
   * **Almacenero:** Acceso a almacén, productos, compras, kardex y proveedores.
6. Seleccione el **Estado** (Activo / Inactivo).
7. Presione **"Guardar Usuario"**.

---

## 9.2. Configuración General de la Tienda
En **Configuración** (`/configuracion`):

![Configuración de la Tienda](docs/img/25_configuracion_tienda.png)
*Figura 9.2: Parámetros del negocio y encabezado de tickets.*

Desde esta sección se personalizan los datos oficiales que aparecerán impresos en los comprobantes y en el encabezado del sistema:
* **Nombre Comercial:** *"Florería Bella Flor"*.
* **RUC / Identificación Fiscal:** Número de RUC de la empresa.
* **Logotipo del Negocio:** Imagen oficial que aparecerá en la barra superior y en los tickets.
* **Dirección Física:** Dirección del local comercial.
* **Teléfono / WhatsApp:** Teléfono de contacto que figurará en el comprobante.
* **Símbolo de Moneda:** Configurado en `S/` (Soles).
* **Mensaje de Pie de Ticket:** Mensaje de despedida y agradecimiento (ejemplo: *"¡Gracias por preferir Florería Bella Flor! Flores que alegran el alma."*).
* Presione **"Guardar Configuración"** para aplicar los cambios a nivel general.

---

<div style="page-break-after: always;"></div>

# CAPÍTULO 10: PREGUNTAS FRECUENTES Y SOLUCIÓN DE PROBLEMAS (FAQ)

### ❓ 1. ¿Qué debo hacer si olvidé mi contraseña de acceso?
* En la pantalla de login haga clic en **"¿Olvidaste tu contraseña?"**, ingrese su correo y siga el enlace de recuperación que recibirá en su bandeja. Si no tiene acceso al correo, el **Administrador** puede editar su usuario desde el módulo de Usuarios y cambiarle la clave manualmente.

---

### ❓ 2. El lector de código de barras no agrega el producto al carrito del POS.
* Verifique que el cursor del ratón se encuentre posicionado dentro del campo de búsqueda del POS.
* Asegúrese de que el producto tenga registrado su código de barras en el módulo de Productos.

---

### ❓ 3. ¿Cómo anulo una venta que fue registrada por error?
* Únicamente el usuario con rol de **Administrador** tiene privilegios para anular ventas. Ingrese al módulo **POS 👉 Listado de Ventas**, busque el comprobante y presione el botón **"Anular"**. El sistema reintegrará automáticamente los productos al stock del inventario.

---

### ❓ 4. El sistema me indica "Stock Insuficiente" al intentar vender un producto.
* Esto ocurre porque el producto tiene existencia en 0 o menor a la cantidad solicitada. El Almacenero o Administrador debe registrar una compra a proveedor o realizar un ajuste de inventario si físicamente sí hay flores en el local.

---

### ❓ 5. ¿Cómo configuro la impresora de tickets?
* Conecte su impresora térmica a la computadora e instale su controlador oficial.
* En el navegador, al presionar **"Imprimir Ticket"**, configure el tamaño de papel en `80mm` o `58mm` y márgenes en `"Ninguno"`.

---

<div align="center">
<br>

**FLORERÍA BELLA FLOR POS**  
*Manual de Usuario del Sistema - Versión 1.0 Final*  
*Todos los derechos reservados © 2026*
</div>
