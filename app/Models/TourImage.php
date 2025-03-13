<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourImage extends BaseModel
{
  protected $table = 'tour_images';
  protected $fillable = ['tour_id', 'image_url', 'created_by', 'updated_by', 'deleted_by'];

  public function tour()
  {
    return $this->belongsTo(Tour::class, 'tour_id');
  }
}
