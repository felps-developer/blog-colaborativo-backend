<?php

namespace Tests\Unit;

use App\Modules\Posts\Dto\CreatePostDto;
use App\Modules\Posts\Dto\UpdatePostDto;
use App\Modules\Posts\Entities\Post;
use App\Modules\Posts\PostsController;
use App\Modules\Posts\PostsService;
use App\Modules\Users\Entities\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class PostsControllerTest extends TestCase
{
    private PostsController $postsController;
    private $postsServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->postsServiceMock = Mockery::mock(PostsService::class);
        $this->postsController = new PostsController($this->postsServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_list_all_posts()
    {
        $post1 = new Post();
        $post1->id = 'post-1';
        $post1->title = 'Post 1';
        $post1->created_at = now();
        $post1->updated_at = now();
        $post1->setRelation('author', (object)[
            'id' => 'user-1',
            'name' => 'Author 1',
            'email' => 'author1@example.com',
        ]);

        $post2 = new Post();
        $post2->id = 'post-2';
        $post2->title = 'Post 2';
        $post2->created_at = now();
        $post2->updated_at = now();
        $post2->setRelation('author', (object)[
            'id' => 'user-2',
            'name' => 'Author 2',
            'email' => 'author2@example.com',
        ]);

        $paginator = new LengthAwarePaginator(
            collect([$post1, $post2]),
            2,
            10,
            1
        );

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('only')
            ->once()
            ->with(['page', 'title', 'author_id'])
            ->andReturn([]);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('per_page', 10)
            ->andReturn(10);

        $this->postsServiceMock
            ->shouldReceive('findAll')
            ->once()
            ->with([], 10)
            ->andReturn($paginator);

        $response = $this->postsController->index($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertCount(2, $responseData['data']);
    }

    /** @test */
    public function it_can_show_a_specific_post()
    {
        $postId = 'post-1';
        $post = new Post();
        $post->id = $postId;
        $post->title = 'Test Post';
        $post->content = ['type' => 'doc', 'content' => []];
        $post->created_at = now();
        $post->updated_at = now();
        $post->setRelation('author', (object)[
            'id' => 'user-1',
            'name' => 'Author 1',
            'email' => 'author1@example.com',
        ]);

        $this->postsServiceMock
            ->shouldReceive('findOne')
            ->once()
            ->with($postId)
            ->andReturn($post);

        $response = $this->postsController->show($postId);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($postId, $responseData['id']);
        $this->assertEquals('Test Post', $responseData['title']);
    }

    /** @test */
    public function it_can_create_a_new_post()
    {
        $postData = [
            'title' => 'New Post',
            'content' => ['type' => 'doc', 'content' => []],
        ];

        $userId = 'user-123';
        $author = new User();
        $author->id = $userId;
        $author->name = 'Test User';
        $author->email = 'test@example.com';
        
        $post = new Post();
        $post->id = 'new-post-id';
        $post->title = $postData['title'];
        $post->content = $postData['content'];
        $post->author_id = $userId;
        $post->created_at = now();
        $post->updated_at = now();
        $post->setRelation('author', $author);

        $dtoMock = Mockery::mock(CreatePostDto::class);
        $dtoMock->shouldReceive('toArray')
            ->once()
            ->andReturn($postData);

        // Mock auth()->id() - o helper auth() pode chamar id() diretamente ou via guard()
        $guardMock = Mockery::mock(\Illuminate\Contracts\Auth\Guard::class);
        $guardMock->shouldReceive('id')->andReturn($userId);
        $guardMock->shouldReceive('user')->andReturn(null);
        
        $authManagerMock = Mockery::mock(\Illuminate\Auth\AuthManager::class);
        $authManagerMock->shouldReceive('guard')
            ->with(Mockery::any())
            ->andReturn($guardMock);
        // Permitir chamadas diretas a id() e user() que delegam para o guard
        $authManagerMock->shouldReceive('id')
            ->andReturnUsing(function() use ($guardMock) {
                return $guardMock->id();
            });
        $authManagerMock->shouldReceive('user')
            ->andReturnUsing(function() use ($guardMock) {
                return $guardMock->user();
            });
        $authManagerMock->shouldIgnoreMissing();
        
        $this->app->instance('auth', $authManagerMock);

        // Simplificado: apenas verificar que o service é chamado
        $this->postsServiceMock
            ->shouldReceive('create')
            ->once()
            ->with($postData, Mockery::any())
            ->andReturn($post);

        $response = $this->postsController->store($dtoMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
    }

    /** @test */
    public function it_returns_401_when_creating_post_without_authentication()
    {
        $dtoMock = Mockery::mock(CreatePostDto::class);

        // Mock auth()->id() retornando null
        $guardMock = Mockery::mock(\Illuminate\Contracts\Auth\Guard::class);
        $guardMock->shouldReceive('id')->andReturn(null);
        $guardMock->shouldReceive('user')->andReturn(null);
        
        $authManagerMock = Mockery::mock(\Illuminate\Auth\AuthManager::class);
        $authManagerMock->shouldReceive('guard')
            ->with(Mockery::any())
            ->andReturn($guardMock);
        // Permitir chamadas diretas a id() e user() que delegam para o guard
        $authManagerMock->shouldReceive('id')
            ->andReturnUsing(function() use ($guardMock) {
                return $guardMock->id();
            });
        $authManagerMock->shouldReceive('user')
            ->andReturnUsing(function() use ($guardMock) {
                return $guardMock->user();
            });
        $authManagerMock->shouldIgnoreMissing();
        
        $this->app->instance('auth', $authManagerMock);

        $response = $this->postsController->store($dtoMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Usuário não autenticado', $responseData['message']);
    }

    /** @test */
    public function it_can_update_a_post()
    {
        $postId = 'post-1';
        $userId = 'user-123';
        $postData = [
            'title' => 'Updated Post',
            'content' => ['type' => 'doc', 'content' => []],
        ];

        $author = new User();
        $author->id = $userId;
        $author->name = 'Test User';
        $author->email = 'test@example.com';
        
        $post = new Post();
        $post->id = $postId;
        $post->title = $postData['title'];
        $post->content = $postData['content'];
        $post->author_id = $userId;
        $post->created_at = now();
        $post->updated_at = now();
        $post->setRelation('author', $author);

        $dtoMock = Mockery::mock(UpdatePostDto::class);
        $dtoMock->shouldReceive('toArray')
            ->once()
            ->andReturn($postData);

        // Mock auth()->id() - o helper auth() pode chamar id() diretamente ou via guard()
        $guardMock = Mockery::mock(\Illuminate\Contracts\Auth\Guard::class);
        $guardMock->shouldReceive('id')->andReturn($userId);
        $guardMock->shouldReceive('user')->andReturn(null);
        
        $authManagerMock = Mockery::mock(\Illuminate\Auth\AuthManager::class);
        $authManagerMock->shouldReceive('guard')
            ->with(Mockery::any())
            ->andReturn($guardMock);
        // Permitir chamadas diretas a id() e user() que delegam para o guard
        $authManagerMock->shouldReceive('id')
            ->andReturnUsing(function() use ($guardMock) {
                return $guardMock->id();
            });
        $authManagerMock->shouldReceive('user')
            ->andReturnUsing(function() use ($guardMock) {
                return $guardMock->user();
            });
        $authManagerMock->shouldIgnoreMissing();
        
        $this->app->instance('auth', $authManagerMock);

        // Simplificado: apenas verificar que o service é chamado
        $this->postsServiceMock
            ->shouldReceive('update')
            ->once()
            ->with($postId, $postData, Mockery::any())
            ->andReturn($post);

        $response = $this->postsController->update($dtoMock, $postId);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_can_delete_a_post()
    {
        $postId = 'post-1';
        $userId = 'user-123';

        // Mock auth()->id() - o helper auth() pode chamar id() diretamente ou via guard()
        $guardMock = Mockery::mock(\Illuminate\Contracts\Auth\Guard::class);
        $guardMock->shouldReceive('id')->andReturn($userId);
        $guardMock->shouldReceive('user')->andReturn(null);
        
        $authManagerMock = Mockery::mock(\Illuminate\Auth\AuthManager::class);
        $authManagerMock->shouldReceive('guard')
            ->with(Mockery::any())
            ->andReturn($guardMock);
        // Permitir chamadas diretas a id() e user() que delegam para o guard
        $authManagerMock->shouldReceive('id')
            ->andReturnUsing(function() use ($guardMock) {
                return $guardMock->id();
            });
        $authManagerMock->shouldReceive('user')
            ->andReturnUsing(function() use ($guardMock) {
                return $guardMock->user();
            });
        $authManagerMock->shouldIgnoreMissing();
        
        $this->app->instance('auth', $authManagerMock);

        $this->postsServiceMock
            ->shouldReceive('remove')
            ->once()
            ->with($postId, Mockery::any())
            ->andReturn([
                'success' => true,
                'message' => 'Post removido com sucesso',
            ]);

        $response = $this->postsController->destroy($postId);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Post removido com sucesso', $responseData['message']);
    }

    /** @test */
    public function it_returns_404_when_post_not_found()
    {
        $postId = 'non-existent';

        $this->postsServiceMock
            ->shouldReceive('findOne')
            ->once()
            ->with($postId)
            ->andThrow(new \Illuminate\Database\Eloquent\ModelNotFoundException());

        $response = $this->postsController->show($postId);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }
}

