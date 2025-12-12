<?php

namespace Tests\Unit;

use App\Modules\Auth\AuthService;
use App\Modules\Users\Entities\User;
use App\Modules\Users\UsersRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthServiceTest extends TestCase
{

    private AuthService $authService;
    private $usersRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->usersRepositoryMock = Mockery::mock(UsersRepository::class);
        $this->authService = new AuthService($this->usersRepositoryMock);
        
        // Mock Log facade
        Log::shouldReceive('debug')->andReturnNull();
        Log::shouldReceive('info')->andReturnNull();
        Log::shouldReceive('warning')->andReturnNull();
        Log::shouldReceive('error')->andReturnNull();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_register_a_new_user()
    {
        $userData = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
        ];

        $user = new User();
        $user->id = '123e4567-e89b-12d3-a456-426614174000';
        $user->name = $userData['name'];
        $user->email = $userData['email'];
        $user->password = Hash::make($userData['password']);

        $this->usersRepositoryMock
            ->shouldReceive('findByEmail')
            ->once()
            ->with($userData['email'])
            ->andReturn(null);

        $this->usersRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) use ($userData) {
                return $data['name'] === $userData['name'] 
                    && $data['email'] === $userData['email']
                    && Hash::check($userData['password'], $data['password']);
            }))
            ->andReturn($user);

        JWTAuth::shouldReceive('fromUser')
            ->once()
            ->with($user)
            ->andReturn('fake-jwt-token');

        $result = $this->authService->register($userData);

        $this->assertArrayHasKey('access_token', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertEquals('fake-jwt-token', $result['access_token']);
        $this->assertEquals($user->id, $result['user']['id']);
        $this->assertEquals($user->name, $result['user']['name']);
        $this->assertEquals($user->email, $result['user']['email']);
    }

    /** @test */
    public function it_throws_exception_when_registering_with_existing_email()
    {
        $userData = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
        ];

        $existingUser = new User();
        $existingUser->email = $userData['email'];

        $this->usersRepositoryMock
            ->shouldReceive('findByEmail')
            ->once()
            ->with($userData['email'])
            ->andReturn($existingUser);

        $this->expectException(ValidationException::class);

        $this->authService->register($userData);
    }

    /** @test */
    public function it_can_login_with_valid_credentials()
    {
        $credentials = [
            'email' => 'joao@example.com',
            'password' => 'password123',
        ];

        $user = new User();
        $user->id = '123e4567-e89b-12d3-a456-426614174000';
        $user->name = 'João Silva';
        $user->email = $credentials['email'];
        $user->password = Hash::make($credentials['password']);

        $this->usersRepositoryMock
            ->shouldReceive('findByEmailWithPassword')
            ->once()
            ->with($credentials['email'])
            ->andReturn($user);

        JWTAuth::shouldReceive('fromUser')
            ->once()
            ->with($user)
            ->andReturn('fake-jwt-token');

        $result = $this->authService->login($credentials);

        $this->assertArrayHasKey('access_token', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertEquals('fake-jwt-token', $result['access_token']);
        $this->assertEquals($user->id, $result['user']['id']);
    }

    /** @test */
    public function it_throws_exception_when_login_with_invalid_email()
    {
        $credentials = [
            'email' => 'invalid@example.com',
            'password' => 'password123',
        ];

        $this->usersRepositoryMock
            ->shouldReceive('findByEmailWithPassword')
            ->once()
            ->with($credentials['email'])
            ->andReturn(null);

        $this->expectException(ValidationException::class);

        $this->authService->login($credentials);
    }

    /** @test */
    public function it_throws_exception_when_login_with_invalid_password()
    {
        $credentials = [
            'email' => 'joao@example.com',
            'password' => 'wrongpassword',
        ];

        $user = new User();
        $user->id = '123e4567-e89b-12d3-a456-426614174000';
        $user->email = $credentials['email'];
        $user->password = Hash::make('correctpassword');

        $this->usersRepositoryMock
            ->shouldReceive('findByEmailWithPassword')
            ->once()
            ->with($credentials['email'])
            ->andReturn($user);

        $this->expectException(ValidationException::class);

        $this->authService->login($credentials);
    }

    /** @test */
    public function it_can_get_authenticated_user()
    {
        $user = new User();
        $user->id = '123e4567-e89b-12d3-a456-426614174000';
        $user->name = 'João Silva';
        $user->email = 'joao@example.com';

        // Mock auth()->user() - o helper auth() pode chamar user() diretamente ou via guard()
        $guardMock = Mockery::mock(\Illuminate\Contracts\Auth\Guard::class);
        $guardMock->shouldReceive('user')
            ->once()
            ->andReturn($user);
        
        // Mock o AuthManager - precisa permitir chamadas diretas a user() e id()
        $authManagerMock = Mockery::mock(\Illuminate\Auth\AuthManager::class);
        $authManagerMock->shouldReceive('guard')
            ->with(Mockery::any())
            ->andReturn($guardMock);
        // Permitir chamadas diretas a user() e id() que delegam para o guard
        $authManagerMock->shouldReceive('user')
            ->andReturnUsing(function() use ($guardMock) {
                return $guardMock->user();
            });
        $authManagerMock->shouldReceive('id')
            ->andReturnUsing(function() use ($guardMock) {
                return $guardMock->id();
            });
        $authManagerMock->shouldIgnoreMissing();
        
        $this->app->instance('auth', $authManagerMock);

        $result = $this->authService->me();

        $this->assertEquals($user->id, $result->id);
        $this->assertEquals($user->name, $result->name);
        $this->assertEquals($user->email, $result->email);
    }

    /** @test */
    public function it_throws_exception_when_getting_unauthenticated_user()
    {
        // Mock auth()->user() retornando null
        $guardMock = Mockery::mock(\Illuminate\Contracts\Auth\Guard::class);
        $guardMock->shouldReceive('user')
            ->once()
            ->andReturn(null);
        
        // Mock o AuthManager - precisa permitir chamadas diretas a user() e id()
        $authManagerMock = Mockery::mock(\Illuminate\Auth\AuthManager::class);
        $authManagerMock->shouldReceive('guard')
            ->with(Mockery::any())
            ->andReturn($guardMock);
        // Permitir chamadas diretas a user() e id() que delegam para o guard
        $authManagerMock->shouldReceive('user')
            ->andReturnUsing(function() use ($guardMock) {
                return $guardMock->user();
            });
        $authManagerMock->shouldReceive('id')
            ->andReturnUsing(function() use ($guardMock) {
                return $guardMock->id();
            });
        $authManagerMock->shouldIgnoreMissing();
        
        $this->app->instance('auth', $authManagerMock);

        $this->expectException(\Illuminate\Auth\AuthenticationException::class);

        $this->authService->me();
    }
}

