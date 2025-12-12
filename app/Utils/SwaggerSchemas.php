<?php

namespace App\Utils;

/**
 * Schemas de resposta para documentação Swagger
 * Similar ao padrão usado no projeto grandizoli-backend
 */
class SwaggerSchemas
{
    /**
     * Schema de resposta de autenticação
     */
    public static function getAuthResponseSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'access_token' => [
                    'type' => 'string',
                    'example' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...',
                ],
                'token_type' => [
                    'type' => 'string',
                    'example' => 'bearer',
                ],
                'expires_in' => [
                    'type' => 'integer',
                    'example' => 3600,
                ],
            ],
        ];
    }

    /**
     * Schema de resposta de usuário
     */
    public static function getUserResponseSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'name' => [
                    'type' => 'string',
                    'example' => 'João Silva',
                ],
                'email' => [
                    'type' => 'string',
                    'format' => 'email',
                    'example' => 'joao@example.com',
                ],
                'created_at' => [
                    'type' => 'string',
                    'format' => 'date-time',
                    'example' => '2024-01-01T00:00:00.000000Z',
                ],
                'updated_at' => [
                    'type' => 'string',
                    'format' => 'date-time',
                    'example' => '2024-01-01T00:00:00.000000Z',
                ],
            ],
        ];
    }

    /**
     * Schema de resposta de post
     */
    public static function getPostResponseSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'title' => [
                    'type' => 'string',
                    'example' => 'Meu Primeiro Post',
                ],
                'content' => [
                    'type' => 'object',
                    'example' => ['version' => '1.0', 'content' => '# Título\n\nConteúdo...'],
                ],
                'author' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => [
                            'type' => 'integer',
                            'example' => 1,
                        ],
                        'name' => [
                            'type' => 'string',
                            'example' => 'João Silva',
                        ],
                        'email' => [
                            'type' => 'string',
                            'format' => 'email',
                            'example' => 'joao@example.com',
                        ],
                    ],
                ],
                'created_at' => [
                    'type' => 'string',
                    'format' => 'date-time',
                    'example' => '2024-01-01T00:00:00.000000Z',
                ],
                'updated_at' => [
                    'type' => 'string',
                    'format' => 'date-time',
                    'example' => '2024-01-01T00:00:00.000000Z',
                ],
            ],
        ];
    }

    /**
     * Schema de resposta de lista paginada de posts
     */
    public static function getPostListResponseSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'data' => [
                    'type' => 'array',
                    'items' => ['$ref' => '#/components/schemas/PostResponse'],
                ],
                'total' => [
                    'type' => 'integer',
                    'example' => 100,
                ],
                'page' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'last_page' => [
                    'type' => 'integer',
                    'example' => 10,
                ],
                'per_page' => [
                    'type' => 'integer',
                    'example' => 10,
                ],
            ],
        ];
    }

    /**
     * Schema de resposta de erro
     */
    public static function getErrorResponseSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'message' => [
                    'type' => 'string',
                    'example' => 'Mensagem de erro',
                ],
                'errors' => [
                    'type' => 'object',
                    'description' => 'Detalhes dos erros (quando aplicável)',
                ],
            ],
        ];
    }
}

