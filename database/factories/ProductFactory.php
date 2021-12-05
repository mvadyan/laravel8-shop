<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->realText(rand(40,50));

        return [
            'category_id' => rand(1,4),
            'brand_id' => rand(1,4),
            'name' => $name,
            'content' => $this->faker->realText(rand(100,200)),
            'slug' => Str::slug(substr($name, 0, 50)),
            'price' => rand(1000,2000),
        ];
    }
}
