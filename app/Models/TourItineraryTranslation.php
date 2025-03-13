<?php

namespace App\Models;


class TourItineraryTranslation extends BaseModel
{
	protected $table = 'tour_itinerary_translations';
	protected $fillable = ['itinerary_id', 'locale', 'title', 'description', 'created_by', 'updated_by', 'deleted_by'];

	public function itinerary()
	{
		return $this->belongsTo(TourItinerary::class, 'itinerary_id');
	}
}
