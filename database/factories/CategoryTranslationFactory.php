<?php

namespace Database\Factories;

use App\Models\CategoryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryTranslationFactory extends Factory
{
  protected $model = CategoryTranslation::class;

  public function definition()
  {
    return [
      'category_id' => \App\Models\Category::inRandomOrder()->first()->id,
      'locale' => $this->faker->randomElement(['en', 'es', 'fr']),
      'name' => $this->faker->word,
      'description' => $this->faker->paragraph,
      'created_by' => 1,
      'updated_by' => 1,
      'deleted_by' => null,
    ];
  }
}
