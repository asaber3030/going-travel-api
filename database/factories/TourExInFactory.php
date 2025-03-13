<?php

namespace Database\Factories;

use App\Models\TourExIn;
use Illuminate\Database\Eloquent\Factories\Factory;

class TourExInFactory extends Factory
{
  protected $model = TourExIn::class;

  public function definition()
  {
    return [
      'tour_id' => \App\Models\Tour::factory(), // Creates a new Tour if not overridden
      'title' => $this->faker->sentence(3), // e.g., "Free Breakfast Included"
      'type' => $this->faker->randomElement(['inclusion', 'exclusion']),
      'created_by' => 1,
      'updated_by' => 1,
      'deleted_by' => null,
    ];
  }
}
