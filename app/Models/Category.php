<?php

namespace App\Models;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
	];

	protected function image(): Attribute
	{
		return Attribute::make(
			get: fn(mixed $value) => URL::to($value),
		);
	}

	public function getNameAttribute()
	{
		return $this->translations()->first()->name ?? '';
	}

	public function translations()
	{
		return $this->hasMany(CategoryTranslation::class, 'category_id');
	}
}
