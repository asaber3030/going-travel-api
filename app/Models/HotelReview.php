<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelReview extends Model
{
	protected $table = 'hotel_reviews';

	public function hotel()
	{
		return $this->belongsTo(Hotel::class, 'hotel_id');
	}
}
