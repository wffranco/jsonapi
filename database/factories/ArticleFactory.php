<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method static Article createOne(array $array)
 * @method static hasComments(int $int)
 */
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
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'content' => $this->faker->paragraphs(3, true),
            'slug' => $this->faker->slug(),
            'created_at' => $this->faker->dateTimeBetween('-3 year', '-3 day'),
            'updated_at' => fn ($attributes) => $this->faker->dateTimeBetween($attributes['created_at'], 'now'),
        ];
    }
}
