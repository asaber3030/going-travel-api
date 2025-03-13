<?php

namespace Database\Factories;

use App\Models\TourHighlightTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TourHighlightTranslationFactory extends Factory
{
  protected $model = TourHighlightTranslation::class;

  public function definition()
  {
    return [
      'tour_highlight_id' => \App\Models\TourHighlight::factory(),
      'locale' => $this->faker->randomElement(['en', 'es', 'fr']),
      'title' => $this->faker->sentence(3),
      'created_by' => 1,
      'updated_by' => 1,
      'deleted_by' => null,
    ];
  }
}
