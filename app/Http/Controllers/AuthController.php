<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\BlacklistedToken;
use App\Interfaces\AuthentificationServiceInterface;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthentificationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        // Validation des données entrantes
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        // Tentative d'authentification via le service d'authentification
        $result = $this->authService->authenticate($request->only('login', 'password'));

        // Si l'authentification échoue, renvoyer une réponse d'erreur en JSON
        if (!$result) {
            return response()->json(['error' => 'Les identifiants sont incorrects'], 401);
        }

        // Renvoyer la réponse au format JSON en cas de succès
        return response()->json($result, 200, ['Content-Type' => 'application/json']);
    }


    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required',
        ]);

        $user = User::where('refresh_token', $request->refresh_token)->first();
        if (!$user) {
            return response()->json(['error' => 'Refresh token invalide'], 401);
        }

        BlacklistedToken::create(['token' => $request->refresh_token, 'type' => 'refresh']);

        $this->authService->revokeTokens($user);
        return response()->json($this->authService->generateTokens($user));
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        if ($token) {
            BlacklistedToken::create([
                'token' => $token,
                'type' => 'access',
                'revoked_at' => now(),
            ]);
        }

        $this->authService->logout();
        return response()->json(['message' => 'Déconnexion réussie'], 204);
    }
}
