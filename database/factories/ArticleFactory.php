<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            // 'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'content' => $this->faker->paragraphs(3, true),
            'slug' => $this->faker->slug(),
        ];
    }
}
