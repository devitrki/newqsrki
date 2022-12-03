<?php

namespace App\Exports\Financeacc;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MassClearingExport implements WithMultipleSheets
{
    use Exportable;

    protected $massClearingId;

    function __construct($massClearingId)
    {
        $this->massClearingId = $massClearingId;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new MassClearingResultExport($this->massClearingId);
        $sheets[] = new MassClearingGeneratedExport($this->massClearingId);

        return $sheets;
    }
}
