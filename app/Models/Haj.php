<?php

namespace App\Models;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Haj extends BaseModel
{
	protected $table = 'hajs';
	protected $fillable = [
		'title',
		'description',
		'long_description',
		'price',
		'banner',
		'thumbnail',
		'hotel',
		'meals',
		'transportation_type',
		'depature_date',
		'return_date',
		'notes',
		'cautions',
		'deleted_by',
		'created_by',
		'updated_by',
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

	public function days()
	{
		return $this->hasMany(HajDay::class, 'haj_id');
	}
}
