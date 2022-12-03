<?php

namespace App\Models\Inventory\Usedoil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;
use App\Library\Helper;

class UoSaldoVendorHistory extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;
}
