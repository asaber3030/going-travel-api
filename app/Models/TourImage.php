<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TourImage extends BaseModel
{
  protected $table = 'tour_images';
  protected $fillable = ['tour_id', 'image_url', 'created_by', 'updated_by', 'deleted_by'];

  protected function imageUrl(): Attribute
  {
    return Attribute::make(
      get: fn(mixed $value) => URL::to($value),
    );
  }

  public function tour()
  {
    return $this->belongsTo(Tour::class, 'tour_id');
  }
}
