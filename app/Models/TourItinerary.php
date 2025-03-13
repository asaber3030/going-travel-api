<?php

namespace App\Models;


class TourItinerary extends BaseModel
{
	protected $table = 'tour_itineraries';
	protected $fillable = ['tour_id', 'day_number', 'image', 'meals', 'overnight_location', 'created_by', 'updated_by', 'deleted_by'];

	public function translations()
	{
		return $this->hasMany(TourItineraryTranslation::class, 'tour_itinerary_id');
	}

	public function tour()
	{
		return $this->belongsTo(Tour::class, 'tour_id');
	}
}
