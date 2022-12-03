<?php

namespace App\Exports\Inventory\Usedoil;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;

class UoHistorySaldoVendorExport implements WithMultipleSheets
{
    protected $vendor;
    protected $dateFrom;
    protected $dateUntil;

    function __construct($vendor, $dateFrom, $dateUntil)
    {
        $this->vendor = $vendor;
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        if( $this->vendor != '0' ){
            $vendors = DB::table('uo_vendors')->where('id', $this->vendor)->get();
        } else {
            $vendors = DB::table('uo_vendors')->get();
        }

        foreach ($vendors as $vendor) {
            $sheets[] = new UoHistorySaldoVendorSheet($vendor->id, $vendor->name, $this->dateFrom, $this->dateUntil);
        }

        return $sheets;
    }
}
