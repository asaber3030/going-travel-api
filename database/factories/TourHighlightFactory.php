<?php

namespace Database\Factories;

use App\Models\TourHighlight;
use Illuminate\Database\Eloquent\Factories\Factory;

class TourHighlightFactory extends Factory
{
  protected $model = TourHighlight::class;

  public function definition()
  {
    return [
      'tour_id' => \App\Models\Tour::factory(),
      'image' => $this->faker->imageUrl(),
      'created_by' => 1,
      'updated_by' => 1,
      'deleted_by' => null,
    ];
  }

  public function configure()
  {
    return $this->afterCreating(function (TourHighlight $tourHighlight) {
      \App\Models\TourHighlightTranslation::factory()->create(['tour_highlight_id' => $tourHighlight->id]);
    });
  }
}
