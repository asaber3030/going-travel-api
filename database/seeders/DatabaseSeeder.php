<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Location;
use App\Models\Tour;
use App\Models\TourExIn;
use App\Models\TourHighlight;
use App\Models\TourImage;
use App\Models\TourItinerary;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	public function run(): void
	{
		Category::factory(50)->create();
		Location::factory(50)->create();

		Tour::factory(50)->create();
		TourExIn::factory(50)->create();
		TourHighlight::factory(50)->create();
		TourImage::factory(50)->create();
		TourItinerary::factory(50)->create();
	}
}
