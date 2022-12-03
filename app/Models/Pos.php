<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Pos extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function plants()
    {
        return $this->hasMany(Plant::class);
    }
}
