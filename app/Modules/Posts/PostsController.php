<?php

namespace App\Modules\Posts;

use App\Modules\Posts\Dto\CreatePostDto;
use App\Modules\Posts\Dto\UpdatePostDto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Posts",
 *     description="Endpoints de posts"
 * )
 */
class PostsController
{
    public function __construct(
        private PostsService $postsService
    ) {}

    /**
     * List all posts.
     *
     * @OA\Get(
     *     path="/api/posts",
     *     tags={"Posts"},
     *     summary="Listar todos os posts",
     *     description="Retorna uma lista paginada de posts",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Itens por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="Filtrar por título",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="author_id",
     *         in="query",
     *         description="Filtrar por ID do autor",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de posts",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="total", type="integer", example=100),
     *             @OA\Property(property="page", type="integer", example=1),
     *             @OA\Property(property="last_page", type="integer", example=10),
     *             @OA\Property(property="per_page", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro ao listar posts",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Erro ao listar posts"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     tags={"Posts"},
     *     summary="Buscar um post pelo ID",
     *     description="Retorna os dados de um post específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do post",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do post",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Meu Primeiro Post"),
     *             @OA\Property(property="content", type="object"),
     *             @OA\Property(property="author", type="object"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Post não encontrado")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/posts",
     *     tags={"Posts"},
     *     summary="Criar um novo post",
     *     description="Cria um novo post no sistema",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CreatePostDto")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post criado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Meu Primeiro Post"),
     *             @OA\Property(property="content", type="object"),
     *             @OA\Property(property="author", type="object"),
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
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Erro de validação"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
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
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     tags={"Posts"},
     *     summary="Atualizar um post",
     *     description="Atualiza um post existente",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do post",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdatePostDto")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post atualizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Meu Post Atualizado"),
     *             @OA\Property(property="content", type="object"),
     *             @OA\Property(property="author", type="object"),
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
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Post não encontrado")
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
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     tags={"Posts"},
     *     summary="Remover um post",
     *     description="Remove um post do sistema",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do post",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post removido com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Post removido com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Usuário não autenticado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Usuário não autenticado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Post não encontrado")
     *         )
     *     )
     * )
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

