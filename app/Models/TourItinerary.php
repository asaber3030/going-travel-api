<?php

namespace App\Models;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Request;

class TourItinerary extends BaseModel
{
	protected $table = 'tour_itineraries';
	protected $fillable = ['tour_id', 'day_number', 'image', 'meals', 'overnight_location', 'created_by', 'updated_by', 'deleted_by'];
	protected $appends = ['title', 'description'];

	protected function image(): Attribute
	{
		return Attribute::make(
			get: fn(mixed $value) => URL::to($value),
		);
	}

	public function getTitleAttribute()
	{
		$preferredLang = request()->header('Accept-Language') ?? 'en';

		return $this->translations()
			->where('locale', $preferredLang)
			->pluck('title')
			->first()
			?? $this->translations()->where('locale', 'en')->pluck('title')->first()
			?? 'N/A';
	}

	public function getDescriptionAttribute()
	{
		$preferredLang = request()->header('Accept-Language') ?? 'en';

		return $this->translations()
			->where('locale', $preferredLang)
			->pluck('description')
			->first()
			?? $this->translations()->where('locale', 'en')->pluck('description')->first()
			?? 'N/A';
	}

	public function translations()
	{
		return $this->hasMany(TourItineraryTranslation::class, 'itinerary_id');
	}

	public function tour()
	{
		return $this->belongsTo(Tour::class, 'tour_id');
	}
}
