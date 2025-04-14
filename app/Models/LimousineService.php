<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LimousineService extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'limousine_services';

    protected $fillable = [
        'limousine_id',
        'locale',
        'our_services',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function limousine()
    {
        return $this->belongsTo(Limousine::class);
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
