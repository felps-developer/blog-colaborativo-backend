<?php

namespace App\Utils;

/**
 * Classe utilitária para gerar metadados do Swagger
 * Similar ao swagger.util.ts do projeto grandizoli-backend
 */
class SwaggerHelper
{
    /**
     * Gera os metadados do componente OARequestBody com base no schema fornecido
     *
     * @param string $schema Nome do schema (classe DTO)
     * @param string|null $description Descrição opcional
     * @return array
     */
    public static function getApiBodyOptions(string $schema, ?string $description = null): array
    {
        return [
            'required' => true,
            'description' => $description ?? 'Dados para a operação',
            'content' => [
                'application/json' => [
                    'schema' => [
                        '$ref' => "#/components/schemas/{$schema}",
                    ],
                ],
            ],
        ];
    }

    /**
     * Gera os metadados do componente OAResponse com base no schema fornecido
     *
     * @param int $status Código de status HTTP
     * @param string $schema Nome do schema (classe DTO)
     * @param string|null $description Descrição opcional
     * @return array
     */
    public static function getApiResponseOptions(
        int $status,
        string $schema,
        ?string $description = null
    ): array {
        return [
            'response' => $status,
            'description' => $description ?? 'Operação concluída com sucesso',
            'content' => [
                'application/json' => [
                    'schema' => [
                        '$ref' => "#/components/schemas/{$schema}",
                    ],
                ],
            ],
        ];
    }

    /**
     * Gera os metadados do componente OAResponse para respostas de erro
     *
     * @param int $status Código de status HTTP
     * @param string|null $description Descrição opcional
     * @return array
     */
    public static function getErrorResponseOptions(int $status, ?string $description = null): array
    {
        $defaultDescriptions = [
            400 => 'Requisição inválida',
            401 => 'Não autenticado',
            403 => 'Não autorizado',
            404 => 'Recurso não encontrado',
            422 => 'Erro de validação',
            500 => 'Erro interno do servidor',
        ];

        return [
            'response' => $status,
            'description' => $description ?? $defaultDescriptions[$status] ?? 'Erro na operação',
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'message' => [
                                'type' => 'string',
                                'example' => $description ?? $defaultDescriptions[$status] ?? 'Erro na operação',
                            ],
                            'errors' => [
                                'type' => 'object',
                                'description' => 'Detalhes dos erros (quando aplicável)',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}

