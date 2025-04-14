<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'image', 'location_id', 'category_id',
        'created_by', 'updated_by', 'deleted_by'
    ];

    public function translations()
    {
        return $this->hasMany(HotelTranslation::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updated_by_user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleted_by_user()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
