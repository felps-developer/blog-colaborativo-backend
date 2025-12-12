<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        // Se for uma requisição API, retorna JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse
     */
    protected function handleApiException(Request $request, Throwable $e): JsonResponse
    {
        // HttpResponseException já tem resposta formatada
        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        }

        // ValidationException
        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);
        }

        // ModelNotFoundException
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'Recurso não encontrado',
            ], 404);
        }

        // Outras exceções
        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        $message = $e->getMessage() ?: 'Erro interno do servidor';

        // Em produção, não expor detalhes da exceção
        if (!config('app.debug')) {
            $message = 'Erro interno do servidor';
        }

        return response()->json([
            'message' => $message,
        ], $statusCode);
    }
}

