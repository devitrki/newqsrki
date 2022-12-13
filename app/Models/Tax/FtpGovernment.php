<?php

namespace App\Models\Tax;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class FtpGovernment extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;
}
