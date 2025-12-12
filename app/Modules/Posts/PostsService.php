<?php

namespace App\Modules\Posts;

use App\Modules\Posts\PostsRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class PostsService
{
    public function __construct(
        private PostsRepository $postsRepository
    ) {}

    /**
     * Find all posts with pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function findAll(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        try {
            return $this->postsRepository->findAll($filters, $perPage);
        } catch (\Exception $e) {
            Log::error("Erro ao buscar posts paginados: {$e->getMessage()}");
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json(['message' => 'Erro ao buscar posts'], 400)
            );
        }
    }

    /**
     * List all posts.
     *
     * @param array $filters
     * @return Collection
     */
    public function list(array $filters = []): Collection
    {
        try {
            return $this->postsRepository->list($filters);
        } catch (\Exception $e) {
            Log::error("Erro ao listar posts: {$e->getMessage()}");
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json(['message' => 'Erro ao listar posts'], 400)
            );
        }
    }

    /**
     * Find a post by ID.
     *
     * @param string $id
     * @return \App\Modules\Posts\Entities\Post
     */
    public function findOne(string $id): \App\Modules\Posts\Entities\Post
    {
        try {
            $post = $this->postsRepository->findOne($id);
            if (!$post) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Post com ID {$id} não encontrado");
            }
            return $post;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error("Erro ao buscar post: {$e->getMessage()}");
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json(['message' => 'Erro ao buscar post'], 400)
            );
        }
    }

    /**
     * Create a new post.
     *
     * @param array $data
     * @param string $authorId
     * @return \App\Modules\Posts\Entities\Post
     */
    public function create(array $data, string $authorId): \App\Modules\Posts\Entities\Post
    {
        try {
            $data['author_id'] = $authorId;
            $post = $this->postsRepository->create($data);
            Log::info("Post criado com sucesso: {$post->id}");
            return $post;
        } catch (\Exception $e) {
            Log::error("Erro ao criar post: {$e->getMessage()}");
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json(['message' => 'Erro ao criar post'], 400)
            );
        }
    }

    /**
     * Update a post.
     *
     * @param string $id
     * @param array $data
     * @param string $userId
     * @return \App\Modules\Posts\Entities\Post
     */
    public function update(string $id, array $data, string $userId): \App\Modules\Posts\Entities\Post
    {
        try {
            // Verifica se o usuário é o autor do post
            if (!$this->postsRepository->isAuthor($id, $userId)) {
                throw new \Illuminate\Http\Exceptions\HttpResponseException(
                    response()->json(['message' => 'Você não tem permissão para editar este post'], 403)
                );
            }

            $post = $this->postsRepository->update($id, $data);
            Log::info("Post atualizado com sucesso: {$id}");
            return $post;
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar post: {$e->getMessage()}");
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json(['message' => 'Erro ao atualizar post'], 400)
            );
        }
    }

    /**
     * Remove a post.
     *
     * @param string $id
     * @param string $userId
     * @return array
     */
    public function remove(string $id, string $userId): array
    {
        try {
            // Verifica se o usuário é o autor do post
            if (!$this->postsRepository->isAuthor($id, $userId)) {
                throw new \Illuminate\Http\Exceptions\HttpResponseException(
                    response()->json(['message' => 'Você não tem permissão para excluir este post'], 403)
                );
            }

            $this->postsRepository->remove($id);
            Log::info("Post removido com sucesso: {$id}");
            return [
                'success' => true,
                'message' => 'Post removido com sucesso',
            ];
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error("Erro ao remover post: {$e->getMessage()}");
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json(['message' => 'Erro ao remover post'], 400)
            );
        }
    }
}

