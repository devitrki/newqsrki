<?php

namespace App\Models\Inventory\Usedoil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class UoMovement extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getDocumentNumberReverse($id){
        $qMovement = DB::table('uo_movements')
                        ->where('id', $id)
                        ->select('document_number');

        $documentNumber = '';
        if ( $qMovement->count() > 0 ) {
            $movement = $qMovement->first();
            $documentNumber = $movement->document_number;
        }
        return $documentNumber;
    }
}
