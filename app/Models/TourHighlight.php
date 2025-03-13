<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourHighlight extends BaseModel
{
	protected $table = 'tour_highlights';
	protected $fillable = [
		'tour_id',
		'image',
		'created_by',
		'updated_by',
		'deleted_by'
	];

	protected $appends = [
		'title',
	];

	public function getTitleAttribute()
	{
		return $this->translations()->first()->title;
	}

	public function translations()
	{
		return $this->hasMany(TourHighlightTranslation::class, 'tour_highlight_id', 'id');
	}
}
