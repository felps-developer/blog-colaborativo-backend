<?php

namespace App\Modules\Posts\Dto;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="CreatePostDto",
 *     required={"title", "content"},
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Título do post",
 *         maxLength=255,
 *         example="Meu Primeiro Post"
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="object",
 *         description="Conteúdo do post em formato JSON (markdown com várias edições)",
 *         example={"version": "1.0", "content": "# Título\n\nConteúdo do post..."}
 *     )
 * )
 */
class CreatePostDto extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'array'], // JSON para armazenar markdown com várias edições
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'O título é obrigatório.',
            'title.string' => 'O título deve ser uma string.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'content.required' => 'O conteúdo é obrigatório.',
            'content.array' => 'O conteúdo deve ser um objeto JSON.',
        ];
    }

    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->validated();
    }
}

