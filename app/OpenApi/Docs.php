<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'API BellaFlorPOS',
    version: '1.0.0',
    description: 'API REST del sistema POS de minimarket. Primero haz POST /login, copia el token y pulsa Authorize.'
)]
#[OA\Server(url: 'http://127.0.0.1:8000/api', description: 'Servidor local')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum',
    description: 'Pega solo el token, sin la palabra Bearer.'
)]
#[OA\Tag(name: 'Autenticación')]
#[OA\Tag(name: 'Categorías')]
#[OA\Tag(name: 'Productos')]
#[OA\Tag(name: 'Clientes')]
#[OA\Tag(name: 'Ventas')]
class Docs
{
    // ---------------- Autenticación ----------------

    #[OA\Post(
        path: '/login',
        tags: ['Autenticación'],
        summary: 'Iniciar sesión y obtener token',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'merinobrenda546@gmail.com'),
                    new OA\Property(property: 'password', type: 'string', example: '1234567'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Inicio de sesión correcto. Devuelve el token.'),
            new OA\Response(response: 401, description: 'Credenciales incorrectas'),
            new OA\Response(response: 403, description: 'Cuenta inactiva o correo sin verificar'),
            new OA\Response(response: 422, description: 'Datos incompletos'),
        ]
    )]
    public function login() {}

    #[OA\Post(
        path: '/logout',
        tags: ['Autenticación'],
        summary: 'Cerrar sesión (borra el token)',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Sesión cerrada'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function logout() {}

    #[OA\Get(
        path: '/me',
        tags: ['Autenticación'],
        summary: 'Datos del usuario del token',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Datos del usuario'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function me() {}

    // ---------------- Categorías ----------------

    #[OA\Get(
        path: '/categorias',
        tags: ['Categorías'],
        summary: 'Listar categorías (Administrador y Almacenero)',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Lista de categorías'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin permiso'),
        ]
    )]
    public function categorias() {}

    // ---------------- Productos ----------------

    #[OA\Get(
        path: '/productos',
        tags: ['Productos'],
        summary: 'Listar productos (los 3 roles)',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', description: 'Buscar por nombre, código o código de barras', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'categoria_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'estado', in: 'query', schema: new OA\Schema(type: 'string', enum: ['activo', 'inactivo'])),
            new OA\Parameter(name: 'stock', in: 'query', description: 'Use "bajo" para ver stock bajo', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', example: 10)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de productos'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function productosIndex() {}

    #[OA\Post(
        path: '/productos',
        tags: ['Productos'],
        summary: 'Crear producto (Administrador y Almacenero)',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['codigo', 'nombre', 'categoria_id', 'unidad_medida_id', 'precio_compra', 'precio_venta'],
                properties: [
                    new OA\Property(property: 'codigo', type: 'string', example: 'PROD-010'),
                    new OA\Property(property: 'codigo_barras', type: 'string', example: '7750000000010'),
                    new OA\Property(property: 'nombre', type: 'string', example: 'Arroz Costeño 1kg'),
                    new OA\Property(property: 'categoria_id', type: 'integer', example: 3),
                    new OA\Property(property: 'unidad_medida_id', type: 'integer', example: 1),
                    new OA\Property(property: 'marca', type: 'string', example: 'Costeño'),
                    new OA\Property(property: 'precio_compra', type: 'number', example: 3.5),
                    new OA\Property(property: 'precio_venta', type: 'number', example: 4.5),
                    new OA\Property(property: 'stock_actual', type: 'number', example: 20),
                    new OA\Property(property: 'stock_minimo', type: 'number', example: 5),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Producto creado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin permiso'),
            new OA\Response(response: 422, description: 'Datos inválidos o código repetido'),
        ]
    )]
    public function productosStore() {}

    #[OA\Get(
        path: '/productos/{id}',
        tags: ['Productos'],
        summary: 'Ver un producto (los 3 roles)',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Detalle del producto'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'Producto no encontrado'),
        ]
    )]
    public function productosShow() {}

    #[OA\Put(
        path: '/productos/{id}',
        tags: ['Productos'],
        summary: 'Editar producto (Administrador y Almacenero). No cambia el stock.',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['codigo', 'nombre', 'categoria_id', 'unidad_medida_id', 'precio_compra', 'precio_venta', 'estado'],
                properties: [
                    new OA\Property(property: 'codigo', type: 'string', example: 'PROD-010'),
                    new OA\Property(property: 'nombre', type: 'string', example: 'Arroz Costeño 1kg'),
                    new OA\Property(property: 'categoria_id', type: 'integer', example: 3),
                    new OA\Property(property: 'unidad_medida_id', type: 'integer', example: 1),
                    new OA\Property(property: 'precio_compra', type: 'number', example: 3.5),
                    new OA\Property(property: 'precio_venta', type: 'number', example: 4.8),
                    new OA\Property(property: 'stock_minimo', type: 'number', example: 5),
                    new OA\Property(property: 'estado', type: 'string', example: 'activo'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Producto actualizado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin permiso'),
            new OA\Response(response: 404, description: 'Producto no encontrado'),
            new OA\Response(response: 422, description: 'Datos inválidos'),
        ]
    )]
    public function productosUpdate() {}

    #[OA\Delete(
        path: '/productos/{id}',
        tags: ['Productos'],
        summary: 'Desactivar producto (solo Administrador)',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Producto desactivado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin permiso'),
            new OA\Response(response: 404, description: 'Producto no encontrado'),
        ]
    )]
    public function productosDestroy() {}

    // ---------------- Clientes ----------------

    #[OA\Get(
        path: '/clientes',
        tags: ['Clientes'],
        summary: 'Listar clientes (Administrador y Vendedor)',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', description: 'Buscar por nombre, documento o teléfono', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', example: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de clientes'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin permiso'),
        ]
    )]
    public function clientesIndex() {}

    #[OA\Post(
        path: '/clientes',
        tags: ['Clientes'],
        summary: 'Crear cliente (Administrador y Vendedor)',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['tipo_documento', 'nombres'],
                properties: [
                    new OA\Property(property: 'tipo_documento', type: 'string', enum: ['DNI', 'RUC', 'CE', 'OTRO'], example: 'DNI'),
                    new OA\Property(property: 'numero_documento', type: 'string', example: '87654321'),
                    new OA\Property(property: 'nombres', type: 'string', example: 'Ana'),
                    new OA\Property(property: 'apellidos', type: 'string', example: 'Torres'),
                    new OA\Property(property: 'telefono', type: 'string', example: '999888777'),
                    new OA\Property(property: 'email', type: 'string', example: 'ana@correo.com'),
                    new OA\Property(property: 'direccion', type: 'string', example: 'Av. Lima 123'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Cliente creado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin permiso'),
            new OA\Response(response: 422, description: 'Datos inválidos o documento repetido'),
        ]
    )]
    public function clientesStore() {}

    // ---------------- Ventas ----------------

    #[OA\Get(
        path: '/ventas',
        tags: ['Ventas'],
        summary: 'Listar ventas (Administrador: todas. Vendedor: solo las suyas)',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'fecha', in: 'query', description: 'Formato AAAA-MM-DD', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'estado', in: 'query', schema: new OA\Schema(type: 'string', enum: ['pendiente', 'pagada', 'anulada'])),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', example: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de ventas'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin permiso'),
        ]
    )]
    public function ventasIndex() {}

    #[OA\Post(
        path: '/ventas',
        tags: ['Ventas'],
        summary: 'Cobrar una venta (Administrador y Vendedor). Descuenta stock y registra Kardex y pago.',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['metodo_pago_id', 'items'],
                properties: [
                    new OA\Property(property: 'cliente_id', type: 'integer', nullable: true, example: null),
                    new OA\Property(property: 'metodo_pago_id', type: 'integer', example: 1),
                    new OA\Property(property: 'monto_recibido', type: 'number', example: 50),
                    new OA\Property(property: 'referencia_pago', type: 'string', example: ''),
                    new OA\Property(property: 'observaciones', type: 'string', example: 'Venta desde Swagger'),
                    new OA\Property(
                        property: 'items',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'producto_id', type: 'integer', example: 1),
                                new OA\Property(property: 'cantidad', type: 'number', example: 2),
                            ],
                            type: 'object'
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Venta cobrada'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin permiso'),
            new OA\Response(response: 422, description: 'Stock insuficiente o datos inválidos'),
        ]
    )]
    public function ventasStore() {}

    #[OA\Get(
        path: '/ventas/{id}',
        tags: ['Ventas'],
        summary: 'Ver una venta con su detalle y pago',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Detalle de la venta'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin permiso (venta de otro vendedor)'),
            new OA\Response(response: 404, description: 'Venta no encontrada'),
        ]
    )]
    public function ventasShow() {}
}
