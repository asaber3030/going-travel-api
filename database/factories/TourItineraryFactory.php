<?php

namespace Database\Factories;

use App\Models\TourItinerary;
use Illuminate\Database\Eloquent\Factories\Factory;

class TourItineraryFactory extends Factory
{
	protected $model = TourItinerary::class;

	public function definition()
	{
		return [
			'tour_id' => \App\Models\Tour::factory(),
			'day_number' => $this->faker->numberBetween(1, 7),
			'image' => $this->faker->imageUrl(),
			'meals' => $this->faker->randomElement(['Breakfast', 'Lunch', 'Dinner', 'Breakfast and Lunch', 'All Meals', 'None']),
			'overnight_location' => $this->faker->city,
			'created_by' => 1,
			'updated_by' => 1,
			'deleted_by' => null,
		];
	}
}
