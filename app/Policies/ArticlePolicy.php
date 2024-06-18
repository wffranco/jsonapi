<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function create(User $user): bool
    {
        return $user->tokenCan('article:create');
    }

    public function update(User $user, Article $article): bool
    {
        return $user->is($article->author) && $user->tokenCan('article:update');
    }

    public function delete(User $user, Article $article): bool
    {
        return $user->is($article->author) && $user->tokenCan('article:delete');
    }
}
