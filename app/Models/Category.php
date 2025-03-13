<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

	public function getNameAttribute()
	{
		return $this->translations()->first()->name ?? '';
	}

	public function translations()
	{
		return $this->hasMany(CategoryTranslation::class, 'category_id');
	}
}
