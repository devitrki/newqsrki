<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Country extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }
}
