<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * POST /api/login
     * Devuelve un token Bearer si las credenciales son correctas.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::with('rol')->where('email', $request->input('email'))->first();

        // Usuario inexistente, sin rol o contraseña incorrecta -> 401
        if (!$user || !$user->rol || !Hash::check($request->input('password'), $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas.'], 401);
        }

        // Cuenta inactiva -> 403
        if ($user->estado !== 'activo') {
            return response()->json([
                'message' => 'Su cuenta está inactiva. Contacte al administrador de la tienda.',
            ], 403);
        }

        // Correo sin verificar -> 403 (igual que en el sistema web)
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Debe verificar su correo electrónico antes de usar la API.',
            ], 403);
        }

        $user->forceFill(['ultimo_acceso' => now()])->save();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message'    => 'Inicio de sesión correcto.',
            'token_type' => 'Bearer',
            'token'      => $token,
            'user'       => new UserResource($user),
        ], 200);
    }

    /**
     * POST /api/logout
     * Borra el token que se está usando.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.'], 200);
    }

    /**
     * GET /api/me
     * Datos del usuario dueño del token.
     */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user()->load('rol'));
    }
}
