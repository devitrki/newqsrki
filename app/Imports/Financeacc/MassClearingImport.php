<?php

namespace App\Imports\Financeacc;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;

use App\Library\Helper;

use App\Models\Financeacc\MassClearing;
use App\Models\Financeacc\MassClearingDetail;
use App\Models\Plant;
use App\Models\BankGl;
use App\Models\SpecialGl;

class MassClearingImport implements ToCollection, WithStartRow
{
    protected $description;

    function __construct($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 3;
    }

    public function collection(Collection $rows)
    {
        $status = 'success';
        $msg = '';

        $insert = [];

        DB::beginTransaction();

        $massClearingId = 0;

        $massClearing = new MassClearing;
        $massClearing->description = $this->description;
        $massClearing->user_id = Auth::id();
        if( $massClearing->save() ){
            $massClearingId = $massClearing->id;
        }

        foreach ($rows as $index => $row) {
            if( trim($row[0]) == '' && trim($row[1]) == '' && trim($row[2]) == '' && trim($row[3]) == '' && trim($row[4]) == '' && trim($row[5]) == '' && trim($row[6]) == '' && trim($row[7]) == ''  && trim($row[8]) == '' && trim($row[9]) == '' ){
                continue;
            }

            $bankInGL = trim($row[0]);
            $bankInDate = trim($row[1]);
            $bankInDescription = trim($row[2]);
            $salesDate = trim($row[3]);
            $salesMonth = trim($row[4]);
            $salesYear = trim($row[5]);
            $specialGL = trim($row[6]);
            $custCodePlant = trim($row[7]);
            $bankInNominal = trim($row[8]);
            $bankInCharge = trim($row[9]);

            $bankGlId = BankGl::getIdbyGl($bankInGL);
            $specialGLId = SpecialGl::getIdbySpecialGl($specialGL);
            $plantId = Plant::getIdByCustomerCode($custCodePlant);

            // check bank gl must already mapping
            if( $bankGlId == 0 ){
                $status = 'failed';
                $msg = "Row " . ((int)$index + 3) . " Col Bank In GL : " . Lang::get("Please check, GL bank has not been mapped");
                DB::rollBack();
                break;
            }

            // check date must date
            if( $bankInDate == '' ){
                $status = 'failed';
                $msg = "Row " . ((int)$index + 3) . " Col Bank In Date : " . Lang::get("Bank in date cannot be empty");
                DB::rollBack();
                break;
            }

            if (is_numeric($bankInDate)) {
                $bankInDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($bankInDate, 'Asia/Jakarta');
            } else {
                $status = 'failed';
                $msg = "Row " . ((int)$index + 3) . " Col Bank In Date : " . Lang::get("Please check, Bank in date must format date");
                DB::rollBack();
                break;
            }

            // check sales date cannot empty
            if( $salesDate == '' ){
                $status = 'failed';
                $msg = "Row " . ((int)$index + 3) . " Col Sales Date : " . Lang::get("Please check, sales date cannot empty");
                DB::rollBack();
                break;
            }

            // check sales date must numeric
            if( !is_numeric($salesMonth) ){
                $status = 'failed';
                $msg = "Row " . ((int)$index + 3) . " Col Sales Month : " . Lang::get("Please check, sales month must number");
                DB::rollBack();
                break;
            }

            // check sales date must numeric
            if( !is_numeric($salesYear) ){
                $status = 'failed';
                $msg = "Row " . ((int)$index + 3) . " Col Sales Year : " . Lang::get("Please check, sales year must number");
                DB::rollBack();
                break;
            }

            // check special gl must already mapping
            if( $specialGLId == 0 ){
                $status = 'failed';
                $msg = "Row " . ((int)$index + 3) . " Col Special GL : " . Lang::get("Please check, Special GL has not been mapped");
                DB::rollBack();
                break;
            }

            // check customer code must already mapping
            if($plantId == 0){
                $status = 'failed';
                $msg = "Row " . ((int)$index + 3) . " Col Outlet Code : " . Lang::get("Please check, Outlet code has not been mapped");
                DB::rollBack();
                break;
            }

            // check bank in nominal cannot empty and must numeric
            if( !is_numeric($bankInNominal) ){
                $status = 'failed';
                $msg = "Row " . ((int)$index + 3) . " Col Total Nominal : " . Lang::get("Please check, Total nominal must number");
                DB::rollBack();
                break;
            }

            if( $bankInCharge == '' ){
                $bankInCharge = 0;
            } else {
                if( !is_numeric($bankInCharge) ){
                    $status = 'failed';
                    $msg = "Row " . ((int)$index + 3) . " Col Bank Charge / Commission : " . Lang::get("Please check, Bank Charge / Commission must number");
                    DB::rollBack();
                    break;
                }
            }

            $salesDate = Str::of($salesDate)->replace(' ', '');
            $salesDateMultiple = Str::of($salesDate)->contains(',');

            $insert[] = [
                'mass_clearing_id' => $massClearingId,
                'bank_in_bank_gl' => $bankInGL,
                'bank_in_date' => $bankInDate,
                'bank_in_description' => $bankInDescription,
                'sales_date' => $salesDate,
                'sales_month' => $salesMonth,
                'sales_year' => $salesYear,
                'multiple_date' => $salesDateMultiple,
                'special_gl' => $specialGL,
                'plant_id' => $plantId,
                'bank_in_nominal' => $bankInNominal,
                'bank_in_charge' => $bankInCharge,
            ];
        }

        DB::table('mass_clearing_details')->insert($insert);
        DB::commit();

        $return = [
            'status' => $status,
            'message' => $msg,
            'id' => $massClearingId
        ];

        $this->return = $return;
    }

}
