<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LimousineTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'limousine_id',
        'locale',
        'name',
        'description',
        'created_by',
        'updated_by'
    ];

    // Relationships
    public function limousine()
    {
        return $this->belongsTo(Limousine::class);
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
