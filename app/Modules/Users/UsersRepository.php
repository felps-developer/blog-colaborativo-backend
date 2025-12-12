<?php

namespace App\Modules\Users;

use App\Modules\Users\Entities\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class UsersRepository
{
    /**
     * Find a user by ID.
     *
     * @param string $id
     * @return User|null
     */
    public function findOne(string $id): ?User
    {
        try {
            Log::debug("Buscando user com ID: {$id}");
            return User::find($id);
        } catch (\Exception $e) {
            Log::error("Erro ao buscar user {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        try {
            Log::debug("Buscando user com email: {$email}");
            return User::where('email', $email)->first();
        } catch (\Exception $e) {
            Log::error("Erro ao buscar user por email {$email}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Find a user by email including password (for authentication).
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmailWithPassword(string $email): ?User
    {
        try {
            Log::debug("Buscando user com email e senha: {$email}");
            return User::where('email', $email)->first();
        } catch (\Exception $e) {
            Log::error("Erro ao buscar user por email com senha {$email}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * List all users with optional filters.
     *
     * @param array $filters
     * @return Collection
     */
    public function list(array $filters = []): Collection
    {
        try {
            Log::debug("Listando users com filtros: " . json_encode($filters));
            $query = User::query();

            if (isset($filters['email'])) {
                $query->where('email', $filters['email']);
            }

            $results = $query->get();
            Log::debug("Encontrados {$results->count()} users");
            return $results;
        } catch (\Exception $e) {
            Log::error("Erro ao listar users: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Find all users with pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function findAll(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        try {
            $page = $filters['page'] ?? 1;
            Log::debug("Buscando users paginados: página {$page}, limite {$perPage}");

            $query = User::query();

            if (isset($filters['email'])) {
                $query->where('email', 'like', "%{$filters['email']}%");
            }

            if (isset($filters['name'])) {
                $query->where('name', 'like', "%{$filters['name']}%");
            }

            $query->orderBy('name', 'asc');

            $results = $query->paginate($perPage, ['*'], 'page', $page);
            Log::debug("Encontrados {$results->total()} users, {$results->count()} na página atual");
            return $results;
        } catch (\Exception $e) {
            Log::error("Erro ao buscar users paginados: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        try {
            Log::debug("Criando novo user: " . json_encode($data));
            $user = User::create($data);
            Log::debug("User criado com ID: {$user->id}");
            return $user;
        } catch (\Exception $e) {
            Log::error("Erro ao criar user: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Update a user.
     *
     * @param string $id
     * @param array $data
     * @return User
     */
    public function update(string $id, array $data): User
    {
        try {
            Log::debug("Atualizando user ID {$id}: " . json_encode($data));
            $user = $this->findOne($id);

            if (!$user) {
                Log::warning("User com ID {$id} não encontrado para atualização");
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("User com ID {$id} não encontrado");
            }

            $user->update($data);
            $user->refresh();
            Log::debug("User {$id} atualizado com sucesso");
            return $user;
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar user {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Remove a user (soft delete).
     *
     * @param string $id
     * @return bool
     */
    public function remove(string $id): bool
    {
        try {
            Log::debug("Removendo user com ID: {$id}");
            $user = $this->findOne($id);

            if (!$user) {
                Log::warning("User com ID {$id} não encontrado para remoção");
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("User com ID {$id} não encontrado");
            }

            $result = $user->delete();
            Log::debug("User {$id} removido com sucesso");
            return $result;
        } catch (\Exception $e) {
            Log::error("Erro ao remover user {$id}: {$e->getMessage()}");
            throw $e;
        }
    }
}

