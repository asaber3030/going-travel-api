<?php

namespace App\Models;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Request;

class Tour extends BaseModel
{
	protected $fillable = [
		'duration',
		'price',
		'type',
		'availability',
		'banner',
		'thumbnail',
		'trip_information',
		'before_you_go',
		'max_people',
		'price_start',
		'has_offer',
		'category_id',
		'location_id',
		'pickup_location_id',
		'created_by',
		'updated_by',
		'deleted_by',
	];

	protected $appends = [
		'title',
		'description',
		'distance_description',
	];

	protected function banner(): Attribute
	{
		return Attribute::make(
			get: fn(mixed $value) => URL::to($value),
		);
	}

	protected function thumbnail(): Attribute
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

	public function getDistanceDescriptionAttribute()
	{
		$preferredLang = request()->header('Accept-Language') ?? 'en';

		return $this->translations()
			->where('locale', $preferredLang)
			->pluck('distance_description')
			->first()
			?? $this->translations()->where('locale', 'en')->pluck('distance_description')->first()
			?? 'N/A';
	}

	// Relations
	public function translations()
	{
		return $this->hasMany(TourTranslation::class, 'tour_id');
	}

	public function reviews()
	{
		return $this->hasMany(Review::class, 'tour_id');
	}

	public function itineraries()
	{
		return $this->hasMany(TourItinerary::class, 'tour_id');
	}

	public function highlights()
	{
		return $this->hasMany(TourHighlight::class, 'tour_id');
	}

	public function inclusions_exclusions()
	{
		return $this->hasMany(TourExIn::class, 'tour_id');
	}

	public function images()
	{
		return $this->hasMany(TourImage::class, 'tour_id');
	}

	public function location()
	{
		return $this->belongsTo(Location::class, 'location_id');
	}

	public function pickup_location()
	{
		return $this->belongsTo(Location::class, 'pickup_location_id');
	}

	public function category()
	{
		return $this->belongsTo(Category::class, 'category_id');
	}
}
