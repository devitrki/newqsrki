<?php

namespace App\Models\Inventory\Usedoil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class UoDeposit extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;
}
