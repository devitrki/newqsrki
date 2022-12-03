<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Opname extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;
}
