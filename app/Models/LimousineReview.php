<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LimousineReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'limousine_reviews';

    protected $fillable = [
        'limousine_id',
        'user_id',
        'reviewer_name',
        'rating',
        'comment',
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

    public function user()
    {
        return $this->belongsTo(User::class);
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
