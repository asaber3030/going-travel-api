<?php

namespace Database\Factories;

use App\Models\TourTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TourTranslationFactory extends Factory
{
	protected $model = TourTranslation::class;

	public function definition()
	{
		return [
			'tour_id' => \App\Models\Tour::factory(),
			'locale' => $this->faker->randomElement(['en', 'es', 'fr']),
			'title' => $this->faker->sentence(3),
			'distance_description' => $this->faker->sentence(5),
			'description' => $this->faker->paragraph,
			'created_by' => 1,
			'updated_by' => 1,
			'deleted_by' => null,
		];
	}
}
