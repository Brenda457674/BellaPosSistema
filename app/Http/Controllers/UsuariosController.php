<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UsuariosController extends Controller
{
    /**
     * Listado de usuarios del sistema (Solo Administrador).
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $rolId = $request->query('rol_id');
        $estado = $request->query('estado');

        $usuarios = User::with('rol')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nombres', 'like', "%{$q}%")
                      ->orWhere('apellidos', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($rolId, fn($query) => $query->where('rol_id', $rolId))
            ->when($estado, fn($query) => $query->where('estado', $estado))
            ->orderBy('nombres')
            ->paginate(15)
            ->withQueryString();

        $roles = Rol::orderBy('id')->get();

        return view('usuarios.index', compact('usuarios', 'roles', 'q', 'rolId', 'estado'));
    }

    /**
     * Formulario para nuevo usuario.
     */
    public function create()
    {
        $roles = Rol::orderBy('id')->get();

        return view('usuarios.create', compact('roles'));
    }

    /**
     * Guardar usuario y enviar correo de verificación.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombres'   => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:usuarios,email'],
            'telefono'  => ['nullable', 'string', 'max:25'],
            'rol_id'    => ['required', 'integer', 'exists:roles,id'],
            'estado'    => ['required', Rule::in(['activo', 'inactivo', 'pendiente'])],
            'password'  => ['required', 'string', 'min:6'],
        ]);

        $usuario = User::create([
            'nombres'   => $datos['nombres'],
            'apellidos' => $datos['apellidos'],
            'email'     => $datos['email'],
            'telefono'  => $datos['telefono'] ?? null,
            'rol_id'    => $datos['rol_id'],
            'estado'    => $datos['estado'],
            'password'  => Hash::make($datos['password']),
        ]);

        // Enviar correo de verificación por Gmail
        try {
            $usuario->sendEmailVerificationNotification();
        } catch (\Throwable $th) {
            // Si el servicio de correo no responde en local, continuar informando
        }

        return redirect()->route('usuarios.index')
            ->with('ok', 'Usuario creado exitosamente. Se envió el enlace de verificación a su correo.');
    }

    /**
     * Formulario de edición.
     */
    public function edit(User $usuario)
    {
        $roles = Rol::orderBy('id')->get();

        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    /**
     * Actualizar usuario.
     * Regla estricta: Nadie puede desactivarse ni cambiarse el rol a sí mismo.
     */
    public function update(Request $request, User $usuario)
    {
        $esPropioUsuario = ($usuario->id === auth()->id());

        $datos = $request->validate([
            'nombres'   => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('usuarios', 'email')->ignore($usuario->id)],
            'telefono'  => ['nullable', 'string', 'max:25'],
            'rol_id'    => ['required', 'integer', 'exists:roles,id'],
            'estado'    => ['required', Rule::in(['activo', 'inactivo', 'pendiente'])],
            'password'  => ['nullable', 'string', 'min:6'],
        ]);

        if ($esPropioUsuario) {
            if ($datos['estado'] !== 'activo') {
                throw ValidationException::withMessages([
                    'estado' => 'Por seguridad, no puedes desactivar tu propia cuenta de administrador.',
                ]);
            }
            if ((int)$datos['rol_id'] !== (int)$usuario->rol_id) {
                throw ValidationException::withMessages([
                    'rol_id' => 'Por seguridad, no puedes cambiarte el rol a ti mismo.',
                ]);
            }
        }

        $usuario->nombres   = $datos['nombres'];
        $usuario->apellidos = $datos['apellidos'];
        $usuario->email     = $datos['email'];
        $usuario->telefono  = $datos['telefono'] ?? null;
        $usuario->rol_id    = $datos['rol_id'];
        $usuario->estado    = $datos['estado'];

        if (!empty($datos['password'])) {
            $usuario->password = Hash::make($datos['password']);
        }

        $usuario->save();

        return redirect()->route('usuarios.index')->with('ok', 'Usuario actualizado con éxito.');
    }

    /**
     * Desactivar o activar un usuario.
     * Regla estricta: Nadie puede desactivarse a sí mismo.
     */
    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Por seguridad, no puedes desactivar tu propia cuenta de administrador.');
        }

        $nuevoEstado = ($usuario->estado === 'activo') ? 'inactivo' : 'activo';
        $usuario->update(['estado' => $nuevoEstado]);

        $accion = ($nuevoEstado === 'activo') ? 'activada' : 'desactivada';

        return redirect()->route('usuarios.index')
            ->with('ok', "La cuenta de {$usuario->name} fue {$accion} correctamente.");
    }
}
