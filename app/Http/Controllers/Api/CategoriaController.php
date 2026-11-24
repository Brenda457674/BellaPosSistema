<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoriaResource;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    /** GET /api/categorias */
    public function index()
    {
        $categorias = DB::table('categorias')->orderBy('nombre')->get();

        return CategoriaResource::collection($categorias);
    }
}
