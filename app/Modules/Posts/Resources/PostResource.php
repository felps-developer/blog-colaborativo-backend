<?php

namespace App\Modules\Posts\Resources;

use App\Modules\Posts\Entities\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class PostResource
{
    /**
     * Transform a single post to array.
     *
     * @param Post $post
     * @return array
     */
    public static function toArray(Post $post): array
    {
        // Tenta obter o author de diferentes formas
        try {
            // Primeiro tenta usar getRelation se a relação estiver definida
            if ($post->relationLoaded('author') || $post->getRelations()['author'] ?? null) {
                $author = $post->getRelation('author');
            } else {
                // Se não estiver definida, tenta acessar diretamente
                $author = $post->author;
            }
        } catch (\Exception $e) {
            // Se não conseguir acessar o author, retorna null
            $author = null;
        }
        
        return [
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
            'author' => $author ? [
                'id' => $author->id,
                'name' => $author->name,
                'email' => $author->email,
            ] : null,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
        ];
    }

    /**
     * Transform a post to array (list format - without content).
     *
     * @param Post $post
     * @return array
     */
    public static function toListItem(Post $post): array
    {
        // Tenta obter o author de diferentes formas
        try {
            // Primeiro tenta usar getRelation se a relação estiver definida
            if ($post->relationLoaded('author') || isset($post->getRelations()['author'])) {
                $author = $post->getRelation('author');
            } else {
                // Se não estiver definida, tenta acessar diretamente
                $author = $post->author;
            }
        } catch (\Exception $e) {
            // Se não conseguir acessar o author, retorna null
            $author = null;
        }
        
        return [
            'id' => $post->id,
            'title' => $post->title,
            'author' => $author ? [
                'id' => $author->id,
                'name' => $author->name,
                'email' => $author->email,
            ] : null,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
        ];
    }

    /**
     * Transform a collection of posts.
     *
     * @param \Illuminate\Database\Eloquent\Collection $posts
     * @return array
     */
    public static function collection(\Illuminate\Database\Eloquent\Collection $posts): array
    {
        return $posts->map(function ($post) {
            return self::toListItem($post);
        })->toArray();
    }

    /**
     * Transform a paginated collection of posts.
     *
     * @param LengthAwarePaginator $paginator
     * @return array
     */
    public static function paginated(LengthAwarePaginator $paginator): array
    {
        $items = $paginator->map(function ($post) {
            return self::toListItem($post);
        });

        return [
            'data' => $items->values()->all(),
            'total' => $paginator->total(),
            'page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
        ];
    }
}

