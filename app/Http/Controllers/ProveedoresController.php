<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProveedoresController extends Controller
{
    private function reglas(?int $id = null): array
    {
        return [
            'tipo_documento'   => ['required', Rule::in(['RUC', 'DNI', 'OTRO'])],
            'numero_documento' => ['nullable', 'string', 'max:20', Rule::unique('proveedores', 'numero_documento')->ignore($id)],
            'razon_social'     => ['required', 'string', 'max:150'],
            'contacto'         => ['nullable', 'string', 'max:120'],
            'telefono'         => ['nullable', 'string', 'max:25'],
            'email'            => ['nullable', 'email', 'max:150'],
            'direccion'        => ['nullable', 'string', 'max:255'],
            'estado'           => ['required', Rule::in(['activo', 'inactivo'])],
        ];
    }

    private function buscar(int $id)
    {
        return DB::table('proveedores')->where('id', $id)->first() ?? abort(404);
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $proveedores = DB::table('proveedores')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('razon_social', 'like', "%{$q}%")
                      ->orWhere('numero_documento', 'like', "%{$q}%")
                      ->orWhere('contacto', 'like', "%{$q}%");
                });
            })
            ->orderBy('razon_social')
            ->paginate(15)
            ->withQueryString();

        return view('proveedores.index', compact('proveedores', 'q'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate($this->reglas());

        DB::table('proveedores')->insert($datos + [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('proveedores.index')->with('ok', 'Proveedor registrado.');
    }

    public function edit(int $proveedor)
    {
        return view('proveedores.edit', ['proveedor' => $this->buscar($proveedor)]);
    }

    public function update(Request $request, int $proveedor)
    {
        $this->buscar($proveedor);
        $datos = $request->validate($this->reglas($proveedor));

        DB::table('proveedores')->where('id', $proveedor)->update($datos + ['updated_at' => now()]);

        return redirect()->route('proveedores.index')->with('ok', 'Proveedor actualizado.');
    }

    public function destroy(int $proveedor)
    {
        $this->buscar($proveedor);

        // Si ya tiene compras, no se borra: se desactiva para no perder el historial.
        if (DB::table('compras')->where('proveedor_id', $proveedor)->exists()) {
            DB::table('proveedores')->where('id', $proveedor)->update(['estado' => 'inactivo', 'updated_at' => now()]);

            return redirect()->route('proveedores.index')
                ->with('ok', 'Tiene compras registradas, así que se marcó como inactivo en vez de borrarlo.');
        }

        DB::table('proveedores')->where('id', $proveedor)->delete();

        return redirect()->route('proveedores.index')->with('ok', 'Proveedor eliminado.');
    }
}
