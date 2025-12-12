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
        return [
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

