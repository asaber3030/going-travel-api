<?php

namespace App\Models;

class CategoryTranslation extends BaseModel
{
	protected $fillable = [
		'category_id',
		'locale',
		'name',
		'description',
		'created_by',
		'updated_by',
		'deleted_by',
	];

	public function category()
	{
		return $this->belongsTo(Category::class, 'category_id');
	}
}
