<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    protected $table = 'amenity';

    protected $fillable = [
        'hotel_id',
        'free_wifi',
        'spa_wellness_center',
        'fitness_center',
        'gourmet_restaurant',
        'indoor_outdoor_pools',
        'air_conditioning',
        'flat_screen_tv',
        'free_parking',
        'front_desk_24h',
    ];

    // Relationship with Hotel
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
