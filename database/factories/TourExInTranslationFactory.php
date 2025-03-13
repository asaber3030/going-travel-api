<?php

namespace Database\Factories;

use App\Models\TourExInTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TourExInTranslationFactory extends Factory
{
  protected $model = TourExInTranslation::class;

  public function definition()
  {
    return [
      'locale' => $this->faker->randomElement(['en', 'es', 'fr']),
      'title' => $this->faker->sentence(3),
      'exclusion_id' => \App\Models\TourExIn::factory(),
      'created_by' => 1,
      'updated_by' => 1,
      'deleted_by' => null,
    ];
  }
}
