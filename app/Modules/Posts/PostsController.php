<?php

namespace App\Modules\Posts;

use App\Modules\Posts\Dto\CreatePostDto;
use App\Modules\Posts\Dto\UpdatePostDto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostsController
{
    public function __construct(
        private PostsService $postsService
    ) {}

    /**
     * List all posts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['page', 'title', 'author_id']);
            $perPage = $request->input('per_page', 10);

            $posts = $this->postsService->findAll($filters, $perPage);

            // Formata a resposta para mostrar título, autor e data
            $formattedPosts = $posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'author' => [
                        'id' => $post->author->id,
                        'name' => $post->author->name,
                        'email' => $post->author->email,
                    ],
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                ];
            });

            return response()->json([
                'data' => $formattedPosts,
                'total' => $posts->total(),
                'page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar posts',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Show a specific post.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $post = $this->postsService->findOne($id);

            return response()->json([
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'author' => [
                    'id' => $post->author->id,
                    'name' => $post->author->name,
                    'email' => $post->author->email,
                ],
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Post não encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar post',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Create a new post.
     *
     * @param CreatePostDto $dto
     * @return JsonResponse
     */
    public function store(CreatePostDto $dto): JsonResponse
    {
        try {
            $userId = auth()->id();

            if (!$userId) {
                return response()->json([
                    'message' => 'Usuário não autenticado',
                ], 401);
            }

            $post = $this->postsService->create($dto->toArray(), $userId);

            return response()->json([
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'author' => [
                    'id' => $post->author->id,
                    'name' => $post->author->name,
                    'email' => $post->author->email,
                ],
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar post',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update a post.
     *
     * @param UpdatePostDto $dto
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdatePostDto $dto, string $id): JsonResponse
    {
        try {
            $userId = auth()->id();

            if (!$userId) {
                return response()->json([
                    'message' => 'Usuário não autenticado',
                ], 401);
            }

            $post = $this->postsService->update($id, $dto->toArray(), $userId);

            return response()->json([
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'author' => [
                    'id' => $post->author->id,
                    'name' => $post->author->name,
                    'email' => $post->author->email,
                ],
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ], 200);
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            return $e->getResponse();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Post não encontrado',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar post',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete a post.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $userId = auth()->id();

            if (!$userId) {
                return response()->json([
                    'message' => 'Usuário não autenticado',
                ], 401);
            }

            $result = $this->postsService->remove($id, $userId);

            return response()->json($result, 200);
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            return $e->getResponse();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Post não encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir post',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}

