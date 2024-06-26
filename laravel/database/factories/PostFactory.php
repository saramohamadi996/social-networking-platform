<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => rand(1, 10),
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'likes_count' => rand(0, 100),
            'shares_count' => rand(0, 50),
            'view_count' => rand(50, 1000),
        ];
    }
}
