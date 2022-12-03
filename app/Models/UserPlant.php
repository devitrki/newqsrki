<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPlant extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function plants()
    {
        return $this->belongsTo(Plant::class);
    }
}
