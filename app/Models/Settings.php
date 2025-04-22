<?php

namespace App\Models;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Settings extends BaseModel
{
  protected $table = 'settings';
  protected $fillable = [
    'key',
    'value',
    'locale',
    'data',
    'group',
    'is_enabled',
    'icon'
  ];

  protected function icon(): Attribute
  {
    return Attribute::make(
      get: fn(mixed $value) => URL::to($value),
    );
  }
}
