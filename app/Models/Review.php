<?php

namespace App\Models;

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
}
