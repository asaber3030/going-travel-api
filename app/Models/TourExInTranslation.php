<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourExInTranslation extends Model
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
