<?php

namespace App\Models\Logbook;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class LbInvCashier extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;
}
