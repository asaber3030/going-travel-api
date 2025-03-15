<?php

namespace App\Models;

class TourExInTranslation extends BaseModel
{
	protected $table = 'inclusions_exclusions_translations';
	protected $fillable = [
		'locale',
		'title',
		'exclusion_id',
		'created_by',
		'updated_by',
		'deleted_by',
	];

	public function exclusion()
	{
		return $this->belongsTo(TourExIn::class, 'exclusion_id');
	}
}
