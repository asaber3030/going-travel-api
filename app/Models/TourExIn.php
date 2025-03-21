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

  protected $appends = [
    'title',
  ];

  public function getTitleAttribute()
  {
    return $this->translations()->first()->title ?? "N/A";
  }

  public function tour()
  {
    return $this->belongsTo(Tour::class, 'tour_id');
  }

  public function translations()
  {
    return $this->hasMany(TourExInTranslation::class, 'exclusion_id');
  }
}
