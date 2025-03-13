<?php

namespace App\Models;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TourItinerary extends BaseModel
{
	protected $table = 'tour_itineraries';
	protected $fillable = ['tour_id', 'day_number', 'image', 'meals', 'overnight_location', 'created_by', 'updated_by', 'deleted_by'];

	protected function image(): Attribute
	{
		return Attribute::make(
			get: fn(mixed $value) => URL::to($value),
		);
	}

	public function translations()
	{
		return $this->hasMany(TourItineraryTranslation::class, 'tour_itinerary_id');
	}

	public function tour()
	{
		return $this->belongsTo(Tour::class, 'tour_id');
	}
}
