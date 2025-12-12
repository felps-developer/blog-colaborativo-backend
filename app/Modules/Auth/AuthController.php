<?php

namespace App\Modules\Auth;

use App\Modules\Auth\Dto\LoginDto;
use App\Modules\Auth\Dto\RegisterDto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Endpoints de autenticação"
 * )
 */
class AuthController
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Register a new user.
     *
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"Auth"},
     *     summary="Registrar um novo usuário",
     *     description="Cria uma nova conta de usuário no sistema",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegisterDto")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário registrado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Erro de validação"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro ao registrar usuário",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Erro ao registrar usuário"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Auth"},
     *     summary="Fazer login",
     *     description="Autentica um usuário e retorna um token JWT",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginDto")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login realizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Credenciais inválidas"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/auth/me",
     *     tags={"Auth"},
     *     summary="Obter usuário autenticado",
     *     description="Retorna os dados do usuário autenticado",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dados do usuário",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", format="email", example="joao@example.com"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Usuário não autenticado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Usuário não autenticado")
     *         )
     *     )
     * )
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

