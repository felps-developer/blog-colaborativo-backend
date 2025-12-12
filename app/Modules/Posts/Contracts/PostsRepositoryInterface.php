<?php

namespace App\Modules\Posts\Contracts;

use App\Modules\Posts\Entities\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PostsRepositoryInterface
{
    /**
     * Find a post by ID.
     *
     * @param string $id
     * @return Post|null
     */
    public function findOne(string $id): ?Post;

    /**
     * List all posts with optional filters.
     *
     * @param array $filters
     * @return Collection
     */
    public function list(array $filters = []): Collection;

    /**
     * Find all posts with pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function findAll(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new post.
     *
     * @param array $data
     * @return Post
     */
    public function create(array $data): Post;

    /**
     * Update a post.
     *
     * @param string $id
     * @param array $data
     * @return Post
     */
    public function update(string $id, array $data): Post;

    /**
     * Remove a post.
     *
     * @param string $id
     * @return bool
     */
    public function remove(string $id): bool;

    /**
     * Check if user is the author of the post.
     *
     * @param string $postId
     * @param string $userId
     * @return bool
     */
    public function isAuthor(string $postId, string $userId): bool;
}

