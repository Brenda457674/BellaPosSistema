<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Mostrar el formulario de inicio de sesión.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Procesar el inicio de sesión.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $email = $credentials['email'];
        $key = 'login:' . $email;

        // Máximo 5 intentos por 15 minutos por email
        $maxAttempts = 5;
        $decayMinutes = 15;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => "Demasiados intentos de acceso. Inténtelo de nuevo en {$seconds} segundos.",
            ]);
        }

        // Buscar usuario por email
        $user = User::where('email', $email)->first();

        // Verificar que el usuario exista y tenga un rol asignado
        if (!$user || !$user->rol) {
            RateLimiter::hit($key, $decayMinutes * 60);

            throw ValidationException::withMessages([
                'email' => 'Credenciales incorrectas.',
            ]);
        }

        // Verificar que la cuenta esté activa
        if ($user->estado !== 'activo') {
            RateLimiter::hit($key, $decayMinutes * 60);

            throw ValidationException::withMessages([
                'email' => 'Su cuenta está inactiva. Contacte al administrador de la tienda.',
            ]);
        }

        // Verificar contraseña
        if (!Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($key, $decayMinutes * 60);

            throw ValidationException::withMessages([
                'password' => 'Credenciales incorrectas.',
            ]);
        }

        // Iniciar sesión
        Auth::login($user, $request->boolean('remember'));

        // Regenerar sesión por seguridad
        $request->session()->regenerate();

        // Actualizar último acceso
        $user->update(['ultimo_acceso' => now()]);

        // Limpiar intentos fallidos
        RateLimiter::clear($key);

        // Verificar el correo antes de permitir el acceso al sistema
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // Redirigir según el rol del usuario
        return $this->redirectByRole($user->rol->nombre);
    }

    /**
     * Cerrar sesión de forma segura.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirigir al usuario según su rol.
     */
    protected function redirectByRole(string $role)
    {
        return match ($role) {
            'Administrador' => redirect()->route('admin.dashboard'),
            'Vendedor' => redirect()->route('ventas.index'),
            'Almacenero' => redirect()->route('almacen.index'),
            default => redirect()->route('login'),
        };
    }
}
