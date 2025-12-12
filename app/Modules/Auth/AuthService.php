<?php

namespace App\Modules\Auth;

use App\Modules\Users\UsersRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(
        private UsersRepository $usersRepository
    ) {}

    /**
     * Register a new user.
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        try {
            Log::debug("Registrando novo usuário: " . json_encode(['email' => $data['email']]));

            // Verifica se o email já está em uso
            $existingUser = $this->usersRepository->findByEmail($data['email']);
            if ($existingUser) {
                Log::warning("Tentativa de registro com email já existente: {$data['email']}");
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []),
                    ['email' => ['Email já está em uso']]
                );
            }

            // Criptografa a senha
            $data['password'] = Hash::make($data['password']);

            // Cria o usuário
            $user = $this->usersRepository->create($data);

            // Gera o token JWT
            $token = JWTAuth::fromUser($user);

            Log::info("Usuário registrado com sucesso: {$user->id}");

            return [
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ];
        } catch (\Exception $e) {
            Log::error("Erro ao registrar usuário: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Login user.
     *
     * @param array $credentials
     * @return array
     */
    public function login(array $credentials): array
    {
        try {
            Log::debug("Tentativa de login: " . json_encode(['email' => $credentials['email']]));

            // Busca o usuário com senha
            $user = $this->usersRepository->findByEmailWithPassword($credentials['email']);

            if (!$user) {
                Log::warning("Tentativa de login com email não encontrado: {$credentials['email']}");
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []),
                    ['email' => ['Credenciais inválidas']]
                );
            }

            // Verifica a senha
            if (!Hash::check($credentials['password'], $user->password)) {
                Log::warning("Tentativa de login com senha incorreta: {$credentials['email']}");
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []),
                    ['email' => ['Credenciais inválidas']]
                );
            }

            // Gera o token JWT
            $token = JWTAuth::fromUser($user);

            Log::info("Login realizado com sucesso: {$user->id}");

            return [
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ];
        } catch (\Exception $e) {
            Log::error("Erro ao fazer login: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Get authenticated user.
     *
     * @return \App\Modules\Users\Entities\User
     */
    public function me(): \App\Modules\Users\Entities\User
    {
        try {
            $user = auth()->user();
            if (!$user) {
                throw new \Illuminate\Auth\AuthenticationException('Usuário não autenticado');
            }
            return $user;
        } catch (\Exception $e) {
            Log::error("Erro ao buscar usuário autenticado: {$e->getMessage()}");
            throw $e;
        }
    }
}

