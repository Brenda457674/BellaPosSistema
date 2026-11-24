<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  array<string>  $roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $esApi = $request->is('api/*') || $request->expectsJson();

        // Si no hay usuario autenticado
        if (!$request->user()) {
            if ($esApi) {
                return response()->json(['message' => 'No autenticado.'], 401);
            }

            return redirect()->route('login');
        }

        // Si el usuario no está activo
        if ($request->user()->estado !== 'activo') {
            if ($esApi) {
                // Borrar el token actual y denegar
                $token = $request->user()->currentAccessToken();
                if ($token instanceof PersonalAccessToken) {
                    $token->delete();
                }

                return response()->json(['message' => 'Su cuenta está inactiva. Contacte al administrador.'], 403);
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Su cuenta está inactiva. Contacte al administrador.',
            ]);
        }

        $userRole = $request->user()->rol?->nombre;

        // Si el rol del usuario no está en la lista de roles permitidos, denegar acceso
        if (!in_array($userRole, $roles, true)) {
            if ($esApi) {
                return response()->json(['message' => 'No tiene permiso para realizar esta acción.'], 403);
            }

            abort(403, 'No tiene permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
