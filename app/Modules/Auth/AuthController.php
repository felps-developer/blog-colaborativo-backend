<?php

namespace App\Modules\Auth;

use App\Modules\Auth\Dto\LoginDto;
use App\Modules\Auth\Dto\RegisterDto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Register a new user.
     *
     * @param RegisterDto $dto
     * @return JsonResponse
     */
    public function register(RegisterDto $dto): JsonResponse
    {
        try {
            $result = $this->authService->register($dto->toArray());

            return response()->json($result, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao registrar usuário',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Login user.
     *
     * @param LoginDto $dto
     * @return JsonResponse
     */
    public function login(LoginDto $dto): JsonResponse
    {
        try {
            $result = $this->authService->login($dto->toArray());

            return response()->json($result, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Credenciais inválidas',
                'errors' => $e->errors(),
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao fazer login',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Get authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->me();

            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ], 200);
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            return response()->json([
                'message' => 'Usuário não autenticado',
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar usuário',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}

