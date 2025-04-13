<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LimousineFeature extends Model
{
    use SoftDeletes;

    protected $table = 'limousine_features';

    protected $fillable = [
        'limousine_id',
        'locale',
        'vehicle_features',
        'additional_info',
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

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleted_by()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
