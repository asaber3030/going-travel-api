<?php

namespace App\Models;

class TourHighlightTranslation extends BaseModel
{
	protected $table = 'tour_highlights_translations';
	protected $fillable = [
		'tour_highlight_id',
		'locale',
		'title',
		'created_by',
		'updated_by',
		'deleted_by'
	];

	public function tour_highlight()
	{
		return $this->hasMany(TourHighlight::class, 'tour_highlight_id', 'id');
	}
}
