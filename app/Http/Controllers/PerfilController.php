<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    public function edit(Request $request)
    {
        return view('perfil.edit', [
            'usuario' => $request->user()->load('rol')
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $datos = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('usuarios', 'email')->ignore($user->id),
            ],
            'telefono' => ['nullable', 'string', 'max:20'],
        ]);

        $user->fill($datos);

        $cambioCorreo = $user->isDirty('email');

        if ($cambioCorreo) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($cambioCorreo) {
            $user->sendEmailVerificationNotification();

            return redirect()->route('verification.notice');
        }

        return back()->with('status', 'Tus datos se guardaron.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'different:current_password',
            ],
        ], [
            'current_password.current_password' =>
                'La contraseña actual no es correcta.',
            'password.different' =>
                'La nueva contraseña debe ser distinta a la actual.',
        ]);

        $request->user()->update([
            'password' => $request->password,
        ]);

        return back()->with('status', 'Tu contraseña se actualizó.');
    }

    public function destroy(Request $request)
    {
        $usuario = $request->user();

        // El administrador no puede eliminar su cuenta desde esta opción.
        abort_if(
            $usuario->rol?->nombre === 'Administrador',
            403,
            'El administrador no puede eliminar su cuenta desde esta opción.'
        );

        // Confirmar la contraseña actual.
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.required' =>
                'Debes escribir tu contraseña actual.',
            'password.current_password' =>
                'La contraseña actual no es correcta.',
        ]);

        // Cerrar e invalidar la sesión.
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Eliminar la cuenta o desactivarla si tiene registros vinculados (ventas, etc.)
        try {
            $usuario->delete();
        } catch (\Throwable $e) {
            $usuario->update(['estado' => 'inactivo']);
        }

        return redirect()
            ->route('login')
            ->with('status', 'Tu cuenta ha sido eliminada correctamente.');
    }
}
