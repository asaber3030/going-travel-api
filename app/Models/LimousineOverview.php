<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LimousineOverview extends BaseModel
{
    use SoftDeletes;

    protected $table = 'limousine_overviews';

    protected $fillable = [
        'limousine_id',
        'locale',
        'about_vehicle',
        'key_features',
        'available_services',
        'pricing',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function limousine()
    {
        return $this->belongsTo(Limousine::class, 'limousine_id');
    }
}
