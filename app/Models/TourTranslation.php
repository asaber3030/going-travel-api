<?php

namespace App\Models;

class TourTranslation extends BaseModel
{
	protected $fillable = [
		'tour_id',
		'locale',
		'title',
		'distance_description',
		'description',
		'created_by',
		'updated_by',
		'deleted_by',
	];

	public function tour()
	{
		return $this->belongsTo(Tour::class, 'tour_id');
	}
}
