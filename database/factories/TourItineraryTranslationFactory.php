<?php

namespace Database\Factories;

use App\Models\TourItineraryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TourItineraryTranslationFactory extends Factory
{
	protected $model = TourItineraryTranslation::class;

	public function definition()
	{
		return [
			'itinerary_id' => \App\Models\TourItinerary::factory(),
			'locale' => $this->faker->randomElement(['en', 'es', 'fr']),
			'title' => $this->faker->sentence(3),
			'description' => $this->faker->paragraph,
			'created_by' => 1,
			'updated_by' => 1,
			'deleted_by' => null,
		];
	}
}
