<?php

namespace App\Modules\Posts;

use App\Modules\Posts\Entities\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class PostsRepository
{
    /**
     * Find a post by ID.
     *
     * @param string $id
     * @return Post|null
     */
    public function findOne(string $id): ?Post
    {
        try {
            Log::debug("Buscando post com ID: {$id}");
            return Post::with('author:id,name,email')->find($id);
        } catch (\Exception $e) {
            Log::error("Erro ao buscar post {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * List all posts with optional filters.
     *
     * @param array $filters
     * @return Collection
     */
    public function list(array $filters = []): Collection
    {
        try {
            Log::debug("Listando posts com filtros: " . json_encode($filters));
            $query = Post::with('author:id,name,email');

            if (isset($filters['author_id'])) {
                $query->where('author_id', $filters['author_id']);
            }

            if (isset($filters['title'])) {
                $query->where('title', 'like', "%{$filters['title']}%");
            }

            $query->orderBy('created_at', 'desc');

            $results = $query->get();
            Log::debug("Encontrados {$results->count()} posts");
            return $results;
        } catch (\Exception $e) {
            Log::error("Erro ao listar posts: {$e->getMessage()}");
            throw $e;
        }
    }

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
            $page = $filters['page'] ?? 1;
            Log::debug("Buscando posts paginados: página {$page}, limite {$perPage}");

            $query = Post::with('author:id,name,email');

            if (isset($filters['author_id'])) {
                $query->where('author_id', $filters['author_id']);
            }

            if (isset($filters['title'])) {
                $query->where('title', 'like', "%{$filters['title']}%");
            }

            $query->orderBy('created_at', 'desc');

            $results = $query->paginate($perPage, ['*'], 'page', $page);
            Log::debug("Encontrados {$results->total()} posts, {$results->count()} na página atual");
            return $results;
        } catch (\Exception $e) {
            Log::error("Erro ao buscar posts paginados: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Create a new post.
     *
     * @param array $data
     * @return Post
     */
    public function create(array $data): Post
    {
        try {
            Log::debug("Criando novo post: " . json_encode($data));
            $post = Post::create($data);
            $post->load('author:id,name,email');
            Log::debug("Post criado com ID: {$post->id}");
            return $post;
        } catch (\Exception $e) {
            Log::error("Erro ao criar post: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Update a post.
     *
     * @param string $id
     * @param array $data
     * @return Post
     */
    public function update(string $id, array $data): Post
    {
        try {
            Log::debug("Atualizando post ID {$id}: " . json_encode($data));
            $post = $this->findOne($id);

            if (!$post) {
                Log::warning("Post com ID {$id} não encontrado para atualização");
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Post com ID {$id} não encontrado");
            }

            $post->update($data);
            $post->refresh();
            $post->load('author:id,name,email');
            Log::debug("Post {$id} atualizado com sucesso");
            return $post;
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar post {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Remove a post (soft delete).
     *
     * @param string $id
     * @return bool
     */
    public function remove(string $id): bool
    {
        try {
            Log::debug("Removendo post com ID: {$id}");
            $post = $this->findOne($id);

            if (!$post) {
                Log::warning("Post com ID {$id} não encontrado para remoção");
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Post com ID {$id} não encontrado");
            }

            $result = $post->delete();
            Log::debug("Post {$id} removido com sucesso");
            return $result;
        } catch (\Exception $e) {
            Log::error("Erro ao remover post {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Check if user is the author of the post.
     *
     * @param string $postId
     * @param string $userId
     * @return bool
     */
    public function isAuthor(string $postId, string $userId): bool
    {
        try {
            $post = $this->findOne($postId);
            return $post && $post->author_id === $userId;
        } catch (\Exception $e) {
            Log::error("Erro ao verificar autor do post {$postId}: {$e->getMessage()}");
            return false;
        }
    }
}

