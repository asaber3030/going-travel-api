<?php

namespace Database\Factories;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

class TourFactory extends Factory
{
  protected $model = Tour::class;

  public function definition()
  {
    return [
      'duration' => $this->faker->numberBetween(1, 14),
      'type' => $this->faker->randomElement(['public', 'private']),
      'availability' => $this->faker->boolean,
      'banner' => $this->faker->imageUrl(),
      'thumbnail' => $this->faker->imageUrl(),
      'trip_information' => '{ "title": "Trip", "content": "This" }',
      'before_you_go' => '{ "title": "Trip", "content": "This" }',
      'max_people' => $this->faker->numberBetween(1, 20),
      'price_start' => $this->faker->randomFloat(2, 50, 500),
      'has_offer' => $this->faker->boolean,
      'category_id' => \App\Models\Category::inRandomOrder()->first()->id,
      'location_id' => \App\Models\Location::inRandomOrder()->first()->id,
      'pickup_location_id' => \App\Models\Location::inRandomOrder()->first()->id,
      'created_by' => 1,
      'updated_by' => 1,
      'deleted_by' => null,
    ];
  }
}
