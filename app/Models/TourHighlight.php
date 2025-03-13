<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
		return $this->translations()->first()->title;
	}

	public function translations()
	{
		return $this->hasMany(TourHighlightTranslation::class, 'tour_highlight_id', 'id');
	}
}
