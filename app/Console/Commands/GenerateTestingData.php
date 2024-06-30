<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\DB;

class GenerateTestingData extends Command
{
    use Concerns\WriteInline, ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate testing data for the application.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        $this->truncateTables();

        $user = User::factory()->hasArticles(2)->create([
            'alias' => 'johndoe',
            'email' => 'user@email.com',
        ]);
        $this->setPermissions($user);

        $article = Article::factory()->hasComments(5)->create([
            'user_id' => $user->id,
        ]);
        Article::factory()->count(10)->create();

        $this->titled('Token', $user->createToken('testing')->plainTextToken);
        $this->titled('User UUID', $user->id);
        $this->titled('User Name', $user->name);
        $this->titled('User email', $user->email);
        $this->titled('User Permissions', $user->permissions->pluck('name')->join(','));

        $this->line('');

        /** @var Article $article */
        $this->titled('Article ID', $article->slug);
        $this->titled('Category ID', $article->category->slug);
        $this->titled('Comment IDs', $article->comments->pluck('id')->join(','));
    }

    protected function setPermissions(User $user)
    {
        $permissions = collect([
            'article:create',
            'article:update',
            'article:delete',
            'category:create',
            'category:update',
            'category:delete',
            'comment:create',
            'comment:update',
            'comment:delete',
        ]);
        $user->permissions()->createMany(
            $permissions->map(fn ($permission) => ['name' => $permission])->toArray()
        );
    }

    protected function truncateTables()
    {
        // disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::statement('truncate table permission_user');
        DB::statement('truncate table personal_access_tokens');

        Comment::truncate();
        Article::truncate();

        Permission::truncate();
        Category::truncate();
        User::truncate();

        //enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
