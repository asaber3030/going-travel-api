<?php

namespace App\Models;


class TourExIn extends BaseModel
{
  protected $table = 'tour_inclusions_exclusions';
  protected $fillable = [
    'tour_id',
    'title',
    'type',
    'created_by',
    'updated_by',
    'deleted_by',
  ];

  public function tour()
  {
    return $this->belongsTo(Tour::class, 'tour_id');
  }

  public function translations()
  {
    return $this->hasMany(TourExInTranslation::class, 'tour_ex_in_id');
  }
}
