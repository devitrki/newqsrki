<?php

namespace App\Exports\Inventory\Usedoil;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;

class UoHistorySaldoVendorExport implements WithMultipleSheets
{
    protected $companyId;
    protected $vendor;
    protected $dateFrom;
    protected $dateUntil;

    function __construct($companyId, $vendor, $dateFrom, $dateUntil)
    {
        $this->companyId = $companyId;
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
            $vendors = DB::table('uo_vendors')
                        ->where('id', $this->vendor)
                        ->where('company_id', $this->companyId)
                        ->get();
        } else {
            $vendors = DB::table('uo_vendors')
                        ->where('company_id', $this->companyId)
                        ->get();
        }

        foreach ($vendors as $vendor) {
            $sheets[] = new UoHistorySaldoVendorSheet($this->companyId, $vendor->id, $vendor->name, $this->dateFrom, $this->dateUntil);
        }

        return $sheets;
    }
}
