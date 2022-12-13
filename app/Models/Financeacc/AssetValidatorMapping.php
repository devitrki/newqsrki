<?php

namespace App\Models\Financeacc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AssetValidatorMapping extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;
}
