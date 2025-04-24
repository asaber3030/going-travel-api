<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'hotel_id',
        'locale',
        'name',
        'description',
        'short_description',
        'address',
        'policy',
        'room_types',
        'slug'
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
