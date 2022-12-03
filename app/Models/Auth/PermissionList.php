<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PermissionList extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;
}
