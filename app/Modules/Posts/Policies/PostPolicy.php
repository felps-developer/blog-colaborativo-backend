<?php

namespace App\Modules\Posts\Policies;

use App\Modules\Posts\Entities\Post;
use App\Modules\Users\Entities\User;

class PostPolicy
{
    /**
     * Determine if the user can update the post.
     *
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->author_id;
    }

    /**
     * Determine if the user can delete the post.
     *
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->author_id;
    }

    /**
     * Determine if the user can view the post.
     *
     * @param User|null $user
     * @param Post $post
     * @return bool
     */
    public function view(?User $user, Post $post): bool
    {
        // Posts são públicos, mas podemos adicionar lógica aqui se necessário
        return true;
    }
}

