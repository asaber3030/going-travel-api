<?php

namespace App\Models;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;

class LimousineImage extends BaseModel
{
    protected $fillable = [
        'limousine_id',
        'url'
    ];

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => URL::to($value),
        );
    }

    public function limousine()
    {
        return $this->belongsTo(Limousine::class);
    }
}
