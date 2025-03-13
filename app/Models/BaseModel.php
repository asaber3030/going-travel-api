<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
  use SoftDeletes, HasFactory;

  protected $guarded = [];

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
