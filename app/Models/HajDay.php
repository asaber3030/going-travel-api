<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;

class HajDay extends Model
{
	protected $table = 'haj_days';
	protected $fillable = ['haj_id', 'title', 'description', 'icon'];

	protected function icon(): Attribute
	{
		return Attribute::make(
			get: fn(mixed $value) => URL::to($value),
		);
	}
}
