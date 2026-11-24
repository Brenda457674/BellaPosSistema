<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreClienteRequest;
use App\Http\Resources\Api\ClienteResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    /** GET /api/clientes?q=&per_page= */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $porPagina = min(max((int) $request->query('per_page', 15), 1), 50);

        $clientes = DB::table('clientes')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nombres', 'like', "%{$q}%")
                      ->orWhere('apellidos', 'like', "%{$q}%")
                      ->orWhere('numero_documento', 'like', "%{$q}%")
                      ->orWhere('telefono', 'like', "%{$q}%");
                });
            })
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->paginate($porPagina)
            ->withQueryString();

        return ClienteResource::collection($clientes);
    }

    /** POST /api/clientes */
    public function store(StoreClienteRequest $request)
    {
        $datos = $request->validated();
        $datos += ['estado' => 'activo'];

        $id = DB::table('clientes')->insertGetId($datos + [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cliente = DB::table('clientes')->where('id', $id)->first();

        return (new ClienteResource($cliente))->response()->setStatusCode(201);
    }
}
