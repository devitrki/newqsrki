<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettycashCcGl extends Model
{
    use HasFactory;

    public static function getDescPrivilege($privilege){
        if ($privilege == '0') {
            return 'All';
        }

        if ($privilege == '1') {
            return 'Outlet';
        }

        if ($privilege == '2') {
            return 'DC';
        }
    }
}
