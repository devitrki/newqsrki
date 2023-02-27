<?php

namespace App\Models\ExternalVendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TemplateSalesDetail extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;
}
