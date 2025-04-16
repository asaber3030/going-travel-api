<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class Hotel extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'image',
		'location_id',
		'category_id',
		'created_by',
		'updated_by',
		'deleted_by'
	];

	protected $with = ['translations', 'amenity', 'location', 'reviews'];

	protected $appends = [
		'name',
		'description',
		'short_description',
		'address',
		'policy',
		'room_types',
		'slug',
	];

	public function getNameAttribute()
	{
		return $this->translations()

			->pluck('name')
			->first() ?? 'N/A';
	}

	public function getDescriptionAttribute()
	{
		return $this->translations()
			->pluck('description')
			->first() ?? 'N/A';
	}

	public function getAddressAttribute()
	{

		return $this->translations()
			->pluck('address')
			->first() ?? 'N/A';
	}

	public function getSlugAttribute()
	{
		return $this->translations()

			->pluck('slug')
			->first() ?? 'N/A';
	}

	public function getShortDescriptionAttribute()
	{
		return $this->translations()

			->pluck('short_description')
			->first() ?? 'N/A';
	}

	public function getPolicyAttribute()
	{
		return $this->translations()

			->pluck('policy')
			->first() ?? 'N/A';
	}


	public function getRoomTypesAttribute()
	{
		return $this->translations()

			->pluck('room_types')
			->first() ?? 'N/A';
	}

	public function reviews()
	{
		return $this->hasMany(HotelReview::class, 'hotel_id');
	}

	public function translations()
	{
		return $this->hasMany(HotelTranslation::class);
	}

	public function amenity()
	{
		return $this->hasOne(Amenity::class);
	}
	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	public function location()
	{
		return $this->belongsTo(Location::class);
	}

	public function created_by_user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function updated_by_user()
	{
		return $this->belongsTo(User::class, 'updated_by');
	}

	public function deleted_by_user()
	{
		return $this->belongsTo(User::class, 'deleted_by');
	}
}
