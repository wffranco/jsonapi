<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function create(User $user): bool
    {
        return $user->tokenCan('comment:create');
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->is($comment->author) && $user->tokenCan('comment:update');
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->is($comment->author) && $user->tokenCan('comment:delete');
    }
}
