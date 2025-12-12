<?php

namespace Tests\Unit;

use App\Modules\Posts\Contracts\PostsRepositoryInterface;
use App\Modules\Posts\Entities\Post;
use App\Modules\Posts\Policies\PostPolicy;
use App\Modules\Posts\PostsService;
use App\Modules\Users\Entities\User;
use App\Modules\Users\UsersRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class PostsServiceTest extends TestCase
{

    private PostsService $postsService;
    private $postsRepositoryMock;
    private $postPolicyMock;
    private $usersRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->postsRepositoryMock = Mockery::mock(PostsRepositoryInterface::class);
        $this->postPolicyMock = Mockery::mock(PostPolicy::class);
        $this->usersRepositoryMock = Mockery::mock(UsersRepository::class);
        $this->postsService = new PostsService($this->postsRepositoryMock, $this->postPolicyMock, $this->usersRepositoryMock);
        
        // Mock Log facade
        Log::shouldReceive('info')->andReturnNull();
        Log::shouldReceive('error')->andReturnNull();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_find_all_posts_with_pagination()
    {
        $filters = ['page' => 1];
        $perPage = 10;

        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $paginator->shouldReceive('total')->andReturn(100);
        $paginator->shouldReceive('currentPage')->andReturn(1);
        $paginator->shouldReceive('lastPage')->andReturn(10);
        $paginator->shouldReceive('perPage')->andReturn(10);

        $this->postsRepositoryMock
            ->shouldReceive('findAll')
            ->once()
            ->with($filters, $perPage)
            ->andReturn($paginator);

        $result = $this->postsService->findAll($filters, $perPage);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    /** @test */
    public function it_can_find_one_post()
    {
        $postId = '123e4567-e89b-12d3-a456-426614174000';
        
        $post = new Post();
        $post->id = $postId;
        $post->title = 'Test Post';
        $post->content = ['type' => 'doc', 'content' => []];

        $this->postsRepositoryMock
            ->shouldReceive('findOne')
            ->once()
            ->with($postId)
            ->andReturn($post);

        $result = $this->postsService->findOne($postId);

        $this->assertEquals($postId, $result->id);
        $this->assertEquals('Test Post', $result->title);
    }

    /** @test */
    public function it_throws_exception_when_post_not_found()
    {
        $postId = '123e4567-e89b-12d3-a456-426614174000';

        $this->postsRepositoryMock
            ->shouldReceive('findOne')
            ->once()
            ->with($postId)
            ->andReturn(null);

        $this->expectException(ModelNotFoundException::class);

        $this->postsService->findOne($postId);
    }

    /** @test */
    public function it_can_create_a_post()
    {
        $authorId = '123e4567-e89b-12d3-a456-426614174000';
        $data = [
            'title' => 'New Post',
            'content' => ['type' => 'doc', 'content' => []],
        ];

        $post = new Post();
        $post->id = 'post-123';
        $post->title = $data['title'];
        $post->content = $data['content'];
        $post->author_id = $authorId;

        $this->postsRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($postData) use ($data, $authorId) {
                return $postData['title'] === $data['title']
                    && $postData['content'] === $data['content']
                    && $postData['author_id'] === $authorId;
            }))
            ->andReturn($post);

        $result = $this->postsService->create($data, $authorId);

        $this->assertEquals($post->id, $result->id);
        $this->assertEquals($data['title'], $result->title);
    }

    /** @test */
    public function it_can_update_a_post_when_user_is_author()
    {
        $postId = '123e4567-e89b-12d3-a456-426614174000';
        $userId = 'user-123';
        $data = [
            'title' => 'Updated Post',
            'content' => ['type' => 'doc', 'content' => []],
        ];

        $post = new Post();
        $post->id = $postId;
        $post->title = 'Original Post';
        $post->author_id = $userId;

        $updatedPost = new Post();
        $updatedPost->id = $postId;
        $updatedPost->title = $data['title'];
        $updatedPost->content = $data['content'];
        $updatedPost->author_id = $userId;

        $user = new User();
        $user->id = $userId;
        $user->name = 'Test User';
        $user->email = 'test@example.com';

        $this->postsRepositoryMock
            ->shouldReceive('findOne')
            ->once()
            ->with($postId)
            ->andReturn($post);

        $this->usersRepositoryMock
            ->shouldReceive('findOne')
            ->once()
            ->with($userId)
            ->andReturn($user);

        $this->postPolicyMock
            ->shouldReceive('update')
            ->once()
            ->with(Mockery::type(User::class), $post)
            ->andReturn(true);

        $this->postsRepositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($postId, $data)
            ->andReturn($updatedPost);
        
        $result = $this->postsService->update($postId, $data, $userId);

        $this->assertEquals($data['title'], $result->title);
    }

    /** @test */
    public function it_throws_exception_when_updating_post_without_permission()
    {
        $postId = '123e4567-e89b-12d3-a456-426614174000';
        $userId = 'user-123';
        $otherUserId = 'user-456';
        $data = ['title' => 'Updated Post'];

        $post = new Post();
        $post->id = $postId;
        $post->author_id = $otherUserId;

        $user = new User();
        $user->id = $userId;
        $user->name = 'Test User';
        $user->email = 'test@example.com';

        $this->postsRepositoryMock
            ->shouldReceive('findOne')
            ->once()
            ->with($postId)
            ->andReturn($post);

        $this->usersRepositoryMock
            ->shouldReceive('findOne')
            ->once()
            ->with($userId)
            ->andReturn($user);

        $this->postPolicyMock
            ->shouldReceive('update')
            ->once()
            ->with(Mockery::type(User::class), $post)
            ->andReturn(false);

        $this->expectException(HttpResponseException::class);

        $this->postsService->update($postId, $data, $userId);
    }

    /** @test */
    public function it_can_remove_a_post_when_user_is_author()
    {
        $postId = '123e4567-e89b-12d3-a456-426614174000';
        $userId = 'user-123';

        $post = new Post();
        $post->id = $postId;
        $post->author_id = $userId;

        $user = new User();
        $user->id = $userId;
        $user->name = 'Test User';
        $user->email = 'test@example.com';

        $this->postsRepositoryMock
            ->shouldReceive('findOne')
            ->once()
            ->with($postId)
            ->andReturn($post);

        $this->usersRepositoryMock
            ->shouldReceive('findOne')
            ->once()
            ->with($userId)
            ->andReturn($user);

        $this->postPolicyMock
            ->shouldReceive('delete')
            ->once()
            ->with(Mockery::type(User::class), $post)
            ->andReturn(true);

        $this->postsRepositoryMock
            ->shouldReceive('remove')
            ->once()
            ->with($postId)
            ->andReturn(true);

        $result = $this->postsService->remove($postId, $userId);

        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertTrue($result['success']);
    }

    /** @test */
    public function it_throws_exception_when_removing_post_without_permission()
    {
        $postId = '123e4567-e89b-12d3-a456-426614174000';
        $userId = 'user-123';
        $otherUserId = 'user-456';

        $post = new Post();
        $post->id = $postId;
        $post->author_id = $otherUserId;

        $user = new User();
        $user->id = $userId;
        $user->name = 'Test User';
        $user->email = 'test@example.com';

        $this->postsRepositoryMock
            ->shouldReceive('findOne')
            ->once()
            ->with($postId)
            ->andReturn($post);

        $this->usersRepositoryMock
            ->shouldReceive('findOne')
            ->once()
            ->with($userId)
            ->andReturn($user);

        $this->postPolicyMock
            ->shouldReceive('delete')
            ->once()
            ->with(Mockery::type(User::class), $post)
            ->andReturn(false);

        $this->expectException(HttpResponseException::class);

        $this->postsService->remove($postId, $userId);
    }
}

