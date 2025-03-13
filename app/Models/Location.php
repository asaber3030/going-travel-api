<?php

namespace App\Models;

class Location extends BaseModel
{
	protected $fillable = [
		'name',
		'image',
		'map_url',
		'created_by',
		'updated_by',
		'deleted_by',
	];
}
