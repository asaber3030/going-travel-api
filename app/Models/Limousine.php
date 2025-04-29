<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected $appends = [
        'name',
        'description',
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => URL::to($value),
        );
    }

    public function getNameAttribute()
    {
        $preferredLang = request()->header('Accept-Language') ?? 'en';

        return $this->translations()
            ->where('locale', $preferredLang)
            ->pluck('name')
            ->first()
            ?? $this->translations()->where('locale', 'en')->pluck('name')->first()
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
