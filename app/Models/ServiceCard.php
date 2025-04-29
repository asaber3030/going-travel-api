<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Request;

class ServiceCard extends Model
{
  protected $table = 'service_cards';
  protected $fillable = [
    'image',
    'key',
    'enabled',
    'url'
  ];

  protected $appends = [
    'title',
    'description',
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

  public function getDescriptionAttribute()
  {
    $preferredLang = request()->header('Accept-Language') ?? 'en';

    return $this->translations()
      ->where('locale', $preferredLang)
      ->pluck('description')
      ->first()
      ?? $this->translations()->where('locale', 'en')->pluck('description')->first()
      ?? 'N/A';
  }


  protected function image(): Attribute
  {
    return Attribute::make(
      get: fn(mixed $value) => URL::to($value),
    );
  }

  public function translations()
  {
    return $this->hasMany(ServiceCardTranslation::class, 'service_card_id');
  }
}
