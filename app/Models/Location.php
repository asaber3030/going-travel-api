<?php

namespace App\Models;


use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Location extends BaseModel
{
	protected $fillable = [
		'name',
		'image',
		'map_url',
		'created_by',
		'updated_by',
		'deleted_by',
	];

	protected function image(): Attribute
	{
		return Attribute::make(
			get: fn(mixed $value) => URL::to($value),
		);
	}
}
