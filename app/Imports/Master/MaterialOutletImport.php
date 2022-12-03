<?php

namespace App\Imports\Master;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

use App\Models\MaterialOutlet;
use App\Models\Material;

class MaterialOutletImport implements ToModel, WithStartRow, WithValidation
{
    use Importable;

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 3;
    }

    public function rules(): array
    {
        return [
            '0' => [ Rule::exists('companies', 'id') ],
            '1' => [ Rule::exists('materials', 'code') ],
            '2' => [ Rule::in(['0', '1']) ],
            '3' => [ Rule::requiredIf(true) ],
            '4' => [ Rule::in(['0', '1']) ],
            '5' => [ Rule::in(['x', '']) ],
            '6' => [ Rule::requiredIf(true) ]
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '0.exists' => Lang::get('Company ID Not Exist'),
            '1.exists' => Lang::get('Material Code Not Exist'),
            '2.in' => Lang::get('Please choose between 0 (Not Include), 1 (Include)'),
            '3.required' => Lang::get('Data is required'),
            '4.in' => Lang::get('Please choose between 0 (Not Include), 1 (Include)'),
            '5.in' => Lang::get('Please choose between x or empty'),
            '6.required' => Lang::get('Data is required'),
        ];
    }

    public function model(array $row)
    {
        $material = DB::table('materials')->where('code', $row[1])->first();

        $check = DB::table('material_outlets')
                            ->where('company_id', $row[0])
                            ->where('code', $row[1])
                            ->count();

        if( $check <= 0 ){
            return new MaterialOutlet([
                'company_id' => $row[0],
                'code' => $material->code,
                'description'=> $material->description,
                'opname' => $row[2],
                'opname_uom' => $row[3],
                'waste' => $row[4],
                'waste_flag' => ($row[5] == 'x') ? 1 : 0,
                'waste_uom' => $row[6],
            ]);
        }

    }
}
