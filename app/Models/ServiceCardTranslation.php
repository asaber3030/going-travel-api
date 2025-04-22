<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCardTranslation extends Model
{
	protected $table = 'service_card_translations';
	protected $fillable = [
		'service_card_id',
		'locale',
		'title',
		'description'
	];
}
