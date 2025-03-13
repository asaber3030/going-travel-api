<?php

namespace App\Models;

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
		'name',
		'description',
		'distance_description',
	];

	// 

	public function getNameAttribute()
	{
		return $this->translations()->first()->name ?? '';
	}

	public function getDescriptionAttribute()
	{
		return $this->translations()->first()->description ?? '';
	}

	public function getDistanceDescriptionAttribute()
	{
		return $this->translations()->first()->distance_description ?? '';
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
