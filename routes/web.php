
<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PublicoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\UnidadesMedidaController;
use App\Http\Controllers\ConfiguracionTiendaController;
use App\Http\Controllers\ReportesController;

/*
|--------------------------------------------------------------------------
| Cambiar idioma
|--------------------------------------------------------------------------
*/


Route::post('/language', function (Request $request) {

    $request->validate([
        'locale' => ['required', 'in:es,en,it'],
    ]);

    session(['locale' => $request->input('locale')]);

    return redirect()->back();

})->name('language.switch');


/*
|--------------------------------------------------------------------------
| Vista pública de Bella Flor
|--------------------------------------------------------------------------
*/

Route::get('/publico', [PublicoController::class, 'index'])
    ->name('publico.index');


/*
|--------------------------------------------------------------------------
| Página principal
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('publico.index');
});


/*
|--------------------------------------------------------------------------
| Invitados: login y recuperar contraseña
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.post');

    Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])
        ->name('password.request');

    Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])
        ->name('password.reset');

    Route::post('/reset-password', [PasswordResetController::class, 'reset'])
        ->name('password.update');
});


/*
|--------------------------------------------------------------------------
| Sesión iniciada: verificación de correo
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    Route::get('/email/verify', function (Request $request) {

        return $request->user()->hasVerifiedEmail()
            ? redirect()->route('dashboard')
            : view('auth.verify-email');

    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {

        $request->fulfill();

        return redirect()->route('dashboard')
            ->with('status', 'Correo verificado.');

    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {

        if (!$request->user()->hasVerifiedEmail()) {
            $request->user()->sendEmailVerificationNotification();
        }

        return back()->with('status', 'verification-link-sent');

    })->middleware('throttle:3,1')->name('verification.send');

});


/*
|--------------------------------------------------------------------------
| Sistema: requiere autenticación y correo verificado
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Dashboard según rol
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', function () {

        $rol = auth()->user()->rol?->nombre;

        return match ($rol) {
            'Administrador' => redirect()->route('admin.dashboard'),
            'Vendedor' => redirect()->route('ventas.index'),
            'Almacenero' => redirect()->route('almacen.index'),
            default => abort(403, 'No tienes un rol válido.'),
        };

    })->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Mi perfil: los tres roles
    |--------------------------------------------------------------------------
    */

    Route::get('/perfil', [PerfilController::class, 'edit'])
        ->name('perfil.edit');

    Route::put('/perfil', [PerfilController::class, 'update'])
        ->name('perfil.update');

    Route::put('/perfil/password', [PerfilController::class, 'password'])
        ->name('perfil.password');

    Route::delete('/perfil', [PerfilController::class, 'destroy'])
        ->name('perfil.destroy');


    /*
    |--------------------------------------------------------------------------
    | Administrador
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Administrador')->group(function () {

        Route::get('/admin', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');


        // Usuarios

        Route::prefix('usuarios')->name('usuarios.')->group(function () {

            Route::get('/', [UsuariosController::class, 'index'])->name('index');

            Route::get('/create', [UsuariosController::class, 'create'])->name('create');

            Route::post('/', [UsuariosController::class, 'store'])->name('store');

            Route::get('/{usuario}/edit', [UsuariosController::class, 'edit'])
                ->whereNumber('usuario')->name('edit');

            Route::put('/{usuario}', [UsuariosController::class, 'update'])
                ->whereNumber('usuario')->name('update');

            Route::delete('/{usuario}', [UsuariosController::class, 'destroy'])
                ->whereNumber('usuario')->name('destroy');

        });


        // Configuración de tienda

        Route::get('/configuracion', [ConfiguracionTiendaController::class, 'edit'])
            ->name('configuracion.edit');

        Route::put('/configuracion', [ConfiguracionTiendaController::class, 'update'])
            ->name('configuracion.update');


        // Reportes

        Route::get('/reportes', [ReportesController::class, 'index'])
            ->name('reportes.index');

    });


    /*
    |--------------------------------------------------------------------------
    | Productos: Administrador, Almacenero y Vendedor
    |--------------------------------------------------------------------------
    */

    Route::prefix('productos')->name('admin.productos.')->group(function () {

        // Consultar

        Route::middleware('role:Administrador,Almacenero,Vendedor')->group(function () {

            Route::get('/', [ProductosController::class, 'index'])
                ->name('index');

            Route::get('/{id}', [ProductosController::class, 'show'])
                ->whereNumber('id')->name('show');

        });


        // Crear y editar

        Route::middleware('role:Administrador,Almacenero')->group(function () {

            Route::get('/create', [ProductosController::class, 'create'])
                ->name('create');

            Route::post('/store', [ProductosController::class, 'store'])
                ->name('store');

            Route::get('/{id}/edit', [ProductosController::class, 'edit'])
                ->whereNumber('id')->name('edit');

            Route::put('/{id}/update', [ProductosController::class, 'update'])
                ->whereNumber('id')->name('update');

        });


        // Desactivar: solo Administrador

        Route::middleware('role:Administrador')->group(function () {

            Route::delete('/{id}', [ProductosController::class, 'destroy'])
                ->whereNumber('id')->name('destroy');

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Proveedores: Administrador y Almacenero
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Administrador,Almacenero')
        ->prefix('proveedores')
        ->name('proveedores.')
        ->group(function () {

            Route::get('/', [ProveedoresController::class, 'index'])->name('index');

            Route::get('/create', [ProveedoresController::class, 'create'])->name('create');

            Route::post('/', [ProveedoresController::class, 'store'])->name('store');

            Route::get('/{proveedor}/edit', [ProveedoresController::class, 'edit'])
                ->whereNumber('proveedor')->name('edit');

            Route::put('/{proveedor}', [ProveedoresController::class, 'update'])
                ->whereNumber('proveedor')->name('update');

            Route::delete('/{proveedor}', [ProveedoresController::class, 'destroy'])
                ->whereNumber('proveedor')->name('destroy');

        });


    /*
    |--------------------------------------------------------------------------
    | Clientes: Administrador y Vendedor
    |--------------------------------------------------------------------------
    */

    Route::prefix('clientes')->name('clientes.')->group(function () {

        Route::middleware('role:Administrador,Vendedor')->group(function () {

            Route::get('/', [ClientesController::class, 'index'])->name('index');

            Route::get('/create', [ClientesController::class, 'create'])->name('create');

            Route::post('/', [ClientesController::class, 'store'])->name('store');

            Route::get('/{cliente}/edit', [ClientesController::class, 'edit'])
                ->whereNumber('cliente')->name('edit');

            Route::put('/{cliente}', [ClientesController::class, 'update'])
                ->whereNumber('cliente')->name('update');

        });


        // Eliminar: solo Administrador

        Route::middleware('role:Administrador')->group(function () {

            Route::delete('/{cliente}', [ClientesController::class, 'destroy'])
                ->whereNumber('cliente')->name('destroy');

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Compras: Administrador y Almacenero
    |--------------------------------------------------------------------------
    */

    Route::prefix('compras')->name('compras.')->group(function () {

        Route::middleware('role:Administrador,Almacenero')->group(function () {

            Route::get('/', [ComprasController::class, 'index'])->name('index');

            Route::get('/create', [ComprasController::class, 'create'])->name('create');

            Route::post('/', [ComprasController::class, 'store'])->name('store');

            Route::get('/{compra}', [ComprasController::class, 'show'])
                ->whereNumber('compra')->name('show');

        });


        // Anular: solo Administrador

        Route::middleware('role:Administrador')->group(function () {

            Route::post('/{compra}/anular', [ComprasController::class, 'anular'])
                ->whereNumber('compra')->name('anular');

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Inventario: Administrador y Almacenero
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Administrador,Almacenero')
        ->prefix('inventario')
        ->name('inventario.')
        ->group(function () {

            Route::get('/', [InventarioController::class, 'index'])
                ->name('index');

            Route::get('/kardex', [InventarioController::class, 'kardex'])
                ->name('kardex');

            Route::get('/ajuste', [InventarioController::class, 'ajuste'])
                ->name('ajuste');

            Route::post('/ajuste', [InventarioController::class, 'storeAjuste'])
                ->name('ajuste.store');

        });


    /*
    |--------------------------------------------------------------------------
    | Categorías: Administrador y Almacenero
    |--------------------------------------------------------------------------
    */

    Route::prefix('categorias')->name('categorias.')->group(function () {

        Route::middleware('role:Administrador,Almacenero')->group(function () {

            Route::get('/', [CategoriasController::class, 'index'])
                ->name('index');

            Route::get('/create', [CategoriasController::class, 'create'])
                ->name('create');

            Route::post('/', [CategoriasController::class, 'store'])
                ->name('store');

            Route::get('/{categoria}/edit', [CategoriasController::class, 'edit'])
                ->whereNumber('categoria')->name('edit');

            Route::put('/{categoria}', [CategoriasController::class, 'update'])
                ->whereNumber('categoria')->name('update');

        });


        // Desactivar: solo Administrador

        Route::middleware('role:Administrador')->group(function () {

            Route::delete('/{categoria}', [CategoriasController::class, 'destroy'])
                ->whereNumber('categoria')->name('destroy');

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Unidades de medida: Administrador y Almacenero
    |--------------------------------------------------------------------------
    */

    Route::prefix('unidades')->name('unidades.')->group(function () {

        Route::middleware('role:Administrador,Almacenero')->group(function () {

            Route::get('/', [UnidadesMedidaController::class, 'index'])
                ->name('index');

            Route::get('/create', [UnidadesMedidaController::class, 'create'])
                ->name('create');

            Route::post('/', [UnidadesMedidaController::class, 'store'])
                ->name('store');

            Route::get('/{unidad}/edit', [UnidadesMedidaController::class, 'edit'])
                ->whereNumber('unidad')->name('edit');

            Route::put('/{unidad}', [UnidadesMedidaController::class, 'update'])
                ->whereNumber('unidad')->name('update');

        });


        // Eliminar: solo Administrador

        Route::middleware('role:Administrador')->group(function () {

            Route::delete('/{unidad}', [UnidadesMedidaController::class, 'destroy'])
                ->whereNumber('unidad')->name('destroy');

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Punto de venta: Administrador y Vendedor
    |--------------------------------------------------------------------------
    */

    Route::prefix('pos')->name('pos.')->group(function () {

        Route::middleware('role:Administrador,Vendedor')->group(function () {

            Route::get('/', [PosController::class, 'index'])
                ->name('index');

            Route::get('/nueva-venta', [PosController::class, 'create'])
                ->name('create');

            Route::post('/', [PosController::class, 'store'])
                ->name('store');

            Route::get('/{venta}', [PosController::class, 'show'])
                ->whereNumber('venta')->name('show');

        });


        // Anular: solo Administrador

        Route::middleware('role:Administrador')->group(function () {

            Route::post('/{venta}/anular', [PosController::class, 'anular'])
                ->whereNumber('venta')->name('anular');

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Panel del vendedor
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Vendedor,Administrador')->group(function () {

        Route::get('/ventas', function () {

            return view('vendedor.dashboard', [
                'usuario' => auth()->user()
            ]);

        })->name('ventas.index');

    });


    /*
    |--------------------------------------------------------------------------
    | Panel del almacenero
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Almacenero,Administrador')->group(function () {

        Route::get('/almacen', function () {

            return view('almacen.dashboard', [
                'usuario' => auth()->user()
            ]);

        })->name('almacen.index');

    });

});
