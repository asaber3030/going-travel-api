<?php

namespace App\Models;

use Faker\Provider\Base;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LimousineFeature extends BaseModel
{
	use SoftDeletes;

	protected $table = 'limousine_features';

	protected $fillable = [
		'limousine_id',
		'locale',
		'vehicle_features',
		'additional_info',
		'created_by',
		'updated_by',
		'deleted_by',
	];

	protected $dates = ['deleted_at'];

	// Relationships
	public function limousine()
	{
		return $this->belongsTo(Limousine::class, 'limousine_id');
	}
}
