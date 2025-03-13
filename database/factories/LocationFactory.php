<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
  protected $model = Location::class;

  public function definition()
  {
    return [
      'name' => $this->faker->city,
      'image' => $this->faker->imageUrl(),
      'map_url' => $this->faker->url,
      'created_by' => 1,
      'updated_by' => 1,
      'deleted_by' => null,
    ];
  }
}
