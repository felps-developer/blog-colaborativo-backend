<?php

namespace Tests\Unit;

use App\Modules\Auth\AuthController;
use App\Modules\Auth\AuthService;
use App\Modules\Auth\Dto\LoginDto;
use App\Modules\Auth\Dto\RegisterDto;
use App\Modules\Users\Entities\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    private AuthController $authController;
    private $authServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->authServiceMock = Mockery::mock(AuthService::class);
        $this->authController = new AuthController($this->authServiceMock);
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

        $expectedResult = [
            'access_token' => 'fake-jwt-token',
            'user' => [
                'id' => '123e4567-e89b-12d3-a456-426614174000',
                'name' => $userData['name'],
                'email' => $userData['email'],
            ],
        ];

        $dtoMock = Mockery::mock(RegisterDto::class);
        $dtoMock->shouldReceive('toArray')
            ->once()
            ->andReturn($userData);

        $this->authServiceMock
            ->shouldReceive('register')
            ->once()
            ->with($userData)
            ->andReturn($expectedResult);

        $response = $this->authController->register($dtoMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($expectedResult, json_decode($response->getContent(), true));
    }

    /** @test */
    public function it_returns_validation_error_on_register()
    {
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
        ];

        $dtoMock = Mockery::mock(RegisterDto::class);
        $dtoMock->shouldReceive('toArray')
            ->once()
            ->andReturn($userData);

        $validator = \Illuminate\Support\Facades\Validator::make([], []);
        $exception = new ValidationException($validator);

        $this->authServiceMock
            ->shouldReceive('register')
            ->once()
            ->with($userData)
            ->andThrow($exception);

        $response = $this->authController->register($dtoMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Erro de validação', $responseData['message']);
    }

    /** @test */
    public function it_can_login_a_user()
    {
        $credentials = [
            'email' => 'joao@example.com',
            'password' => 'password123',
        ];

        $expectedResult = [
            'access_token' => 'fake-jwt-token',
            'user' => [
                'id' => '123e4567-e89b-12d3-a456-426614174000',
                'name' => 'João Silva',
                'email' => $credentials['email'],
            ],
        ];

        $dtoMock = Mockery::mock(LoginDto::class);
        $dtoMock->shouldReceive('toArray')
            ->once()
            ->andReturn($credentials);

        $this->authServiceMock
            ->shouldReceive('login')
            ->once()
            ->with($credentials)
            ->andReturn($expectedResult);

        $response = $this->authController->login($dtoMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedResult, json_decode($response->getContent(), true));
    }

    /** @test */
    public function it_returns_401_on_invalid_login_credentials()
    {
        $credentials = [
            'email' => 'joao@example.com',
            'password' => 'wrong-password',
        ];

        $dtoMock = Mockery::mock(LoginDto::class);
        $dtoMock->shouldReceive('toArray')
            ->once()
            ->andReturn($credentials);

        $validator = \Illuminate\Support\Facades\Validator::make([], []);
        $exception = new ValidationException($validator);

        $this->authServiceMock
            ->shouldReceive('login')
            ->once()
            ->with($credentials)
            ->andThrow($exception);

        $response = $this->authController->login($dtoMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Credenciais inválidas', $responseData['message']);
    }

    /** @test */
    public function it_can_get_authenticated_user()
    {
        $user = new User();
        $user->id = '123e4567-e89b-12d3-a456-426614174000';
        $user->name = 'João Silva';
        $user->email = 'joao@example.com';
        $user->created_at = now();
        $user->updated_at = now();

        $requestMock = Mockery::mock(Request::class);

        $this->authServiceMock
            ->shouldReceive('me')
            ->once()
            ->andReturn($user);

        $response = $this->authController->me($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($user->id, $responseData['id']);
        $this->assertEquals($user->name, $responseData['name']);
        $this->assertEquals($user->email, $responseData['email']);
    }

    /** @test */
    public function it_returns_401_when_getting_unauthenticated_user()
    {
        $requestMock = Mockery::mock(Request::class);

        $this->authServiceMock
            ->shouldReceive('me')
            ->once()
            ->andThrow(new \Illuminate\Auth\AuthenticationException('Usuário não autenticado'));

        $response = $this->authController->me($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Usuário não autenticado', $responseData['message']);
    }
}

