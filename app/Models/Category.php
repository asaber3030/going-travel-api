<?php

namespace App\Models;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Request;

class Category extends BaseModel
{
	protected $fillable = [
		'image',
		'created_by',
		'updated_by',
		'deleted_by',
	];

	protected $appends = [
		'name',
		'description',
	];

	protected function image(): Attribute
	{
		return Attribute::make(
			get: fn(mixed $value) => URL::to($value),
		);
	}

	public function getNameAttribute()
	{
		$locale = Request::header('Accept-Language', config('app.locale'));
		$language = languageExists($locale) ? $locale : 'en';

		return $this->translations()
			->where('locale', $language)
			->pluck('name')
			->first() ?? 'N/A';
	}

	public function getDescriptionAttribute()
	{
		$locale = Request::header('Accept-Language', config('app.locale'));
		$language = languageExists($locale) ? $locale : 'en';

		return $this->translations()
			->where('locale', $language)
			->pluck('description')
			->first() ?? 'N/A';
	}

	public function translations()
	{
		return $this->hasMany(CategoryTranslation::class, 'category_id');
	}

	public function tours()
	{
		return $this->hasMany(Tour::class, 'category_id');
	}
}
