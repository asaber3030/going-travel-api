<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Request;

class Hotel extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'image',
		'location_id',
		'category_id',
		'status',
		'price',
		'stars',
		'banner',
		'thumbnail',
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

	public function getNameAttribute()
	{
		$preferredLang = request()->header('Accept-Language') ?? 'en';

		return $this->translations()
			->where('locale', $preferredLang)
			->pluck('name')
			->first()
			?? $this->translations()->where('locale', 'en')->pluck('name')->first()
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

	public function getAddressAttribute()
	{

		$preferredLang = request()->header('Accept-Language') ?? 'en';

		return $this->translations()
			->where('locale', $preferredLang)
			->pluck('address')
			->first()
			?? $this->translations()->where('locale', 'en')->pluck('address')->first()
			?? 'N/A';
	}

	public function getSlugAttribute()
	{
		$preferredLang = request()->header('Accept-Language') ?? 'en';

		return $this->translations()
			->where('locale', $preferredLang)
			->pluck('slug')
			->first()
			?? $this->translations()->where('locale', 'en')->pluck('slug')->first()
			?? 'N/A';
	}

	public function getShortDescriptionAttribute()
	{
		$preferredLang = request()->header('Accept-Language') ?? 'en';

		return $this->translations()
			->where('locale', $preferredLang)
			->pluck('short_description')
			->first()
			?? $this->translations()->where('locale', 'en')->pluck('short_description')->first()
			?? 'N/A';
	}

	public function getPolicyAttribute()
	{
		$preferredLang = request()->header('Accept-Language') ?? 'en';

		return $this->translations()
			->where('locale', $preferredLang)
			->pluck('policy')
			->first()
			?? $this->translations()->where('locale', 'en')->pluck('policy')->first()
			?? 'N/A';
	}


	public function getRoomTypesAttribute()
	{
		$preferredLang = request()->header('Accept-Language') ?? 'en';

		return $this->translations()
			->where('locale', $preferredLang)
			->pluck('room_types')
			->first()
			?? $this->translations()->where('locale', 'en')->pluck('room_types')->first()
			?? 'N/A';
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
