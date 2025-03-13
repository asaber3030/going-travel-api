<?php

namespace App\Models;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Review extends BaseModel
{
	protected $fillable = [
		'client_name',
		'tour_id',
		'rating',
		'title',
		'description',
		'image',
		'created_by',
		'updated_by',
		'deleted_by',
	];

	public function tour()
	{
		return $this->belongsTo(Tour::class);
	}
	protected function image(): Attribute
	{
		return Attribute::make(
			get: fn(mixed $value) => URL::to($value),
		);
	}
}
