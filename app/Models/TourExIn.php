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
    $locale = Request::header('Accept-Language', config('app.locale'));
    $language = languageExists($locale) ? $locale : 'en';

    return $this->translations()
      ->where('locale', $language)
      ->pluck('title')
      ->first() ?? 'N/A';
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
