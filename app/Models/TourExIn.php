<?php

namespace App\Models;

use Illuminate\Support\Facades\Request;

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
    $preferredLang = request()->header('Accept-Language') ?? 'en';

    return $this->translations()
      ->where('locale', $preferredLang)
      ->pluck('title')
      ->first()
      ?? $this->translations()->where('locale', 'en')->pluck('title')->first()
      ?? 'N/A';
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
