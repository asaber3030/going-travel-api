<?php

namespace App\Models;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Request;

class TourHighlight extends BaseModel
{
	protected $table = 'tour_highlights';
	protected $fillable = [
		'tour_id',
		'image',
		'created_by',
		'updated_by',
		'deleted_by'
	];

	protected $appends = [
		'title',
	];

	protected function image(): Attribute
	{
		return Attribute::make(
			get: fn(mixed $value) => URL::to($value),
		);
	}

	public function getTitleAttribute()
	{
		$locale = Request::header('Accept-Language', config('app.locale'));
		$language = languageExists($locale) ? $locale : 'en';

		return $this->translations()
			->where('locale', $language)
			->pluck('title')
			->first() ?? 'N/A';
	}

	public function translations()
	{
		return $this->hasMany(TourHighlightTranslation::class, 'tour_highlight_id', 'id');
	}
}
