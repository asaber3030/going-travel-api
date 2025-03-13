<?php

namespace Database\Factories;

use App\Models\TourImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class TourImageFactory extends Factory
{
  protected $model = TourImage::class;

  public function definition()
  {
    return [
      'tour_id' => \App\Models\Tour::factory(),
      'image_url' => $this->faker->imageUrl(),
      'created_by' => 1,
      'updated_by' => 1,
      'deleted_by' => null,
    ];
  }
}
