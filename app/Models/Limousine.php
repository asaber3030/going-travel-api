<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Limousine extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'price_per_hour',
        'max_passengers',
        'image',
        'category_id',
        'location_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(LimousineReview::class);
    }

    public function images()
    {
        return $this->hasMany(LimousineImage::class);
    }

    public function features()
    {
        return $this->hasMany(LimousineFeature::class);
    }

    public function services()
    {
        return $this->hasMany(LimousineService::class);
    }

    public function overviews()
    {
        return $this->hasMany(LimousineOverview::class);
    }

    public function specifications()
    {
        return $this->hasMany(LimousineSpecification::class);
    }

    public function translations()
    {
        return $this->hasMany(LimousineTranslation::class);
    }
}
