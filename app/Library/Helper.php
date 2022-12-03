<?php
namespace App\Library;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use DateTime;

class Helper
{
    /**
     * Function to generate DOM
     * @return String
     * By Yudha Permana
     */
    public static function generateDOM()
    {
        return strtolower('d'.Str::random(7));
    }

    /**
     * Function to generate DOM
     * @return String
     * By Yudha Permana
     */
    public static function generateRandomStr($lenght)
    {
        return strtolower(Str::random($lenght));
    }

    /**
     * Function format to response JSON
     * @return array
     * By Yudha Permana
     */
    public static function resJSON( $status, $message, $data = [] )
    {
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * Function to check data used in another table
     * @return boolean
     * By Yudha Permana
     */
    public static function used( $value, $primary, $tables = [] )
    {
        $used = false;

        foreach ($tables as $table) {
            $count = DB::table($table)->where($primary, $value)->count();
            if( $count > 0 ){
                $used = true;
                break;
            }
        }

        return $used;
    }

    // date

    /**
     * Function to check data used in another table
     * @return boolean
     * By Yudha Permana
     */
    public static function DateDifference( $date1, $date2 )
    {
        $date1 = Carbon::createFromFormat('Y-m-d', $date1);
        $date2 = Carbon::createFromFormat('Y-m-d', $date2);

        return $date1->diffInDays($date2);
    }

    /**
     * Function to convert from format to format
     * @return boolean
     * By Yudha Permana
     */
    public static function DateConvertFormat( $date, $format1, $format2 )
    {
        $date = Carbon::createFromFormat($format1, $date);

        return $date->format($format2);
    }

    /**
     * Function to convert from format to format
     * @return boolean
     * By Yudha Permana
     */
    public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    /**
     * generate ket from timestamps for sap
     * @return String
     */
    public static function getKeySap()
    {
        $key = time() . mt_rand(1000000, 9999999);
        return $key . '';
    }

    // number
    /**
     * generate ket from timestamps for sap
     * @return double
     */
    public static function replaceDelimiterNumber($value, $delimiter1 = ',', $delimiter2 = '.')
    {
        return str_replace($delimiter1, $delimiter2, $value);
    }

    /**
     * convert number to format rupiah
     * @return string
     */
    public static function convertNumberToInd($number, $prefix = 'Rp ', $precision = 2, $delimiterComma = ',', $delimiterThousand = '.')
    {
        $hasil_rupiah = $prefix . number_format($number, $precision, $delimiterComma, $delimiterThousand);
        return $hasil_rupiah;
    }

    // string
    /**
     * get name month (Indonesia) from number of month
     * @return double
     */
    public static function getMonthByNumberMonth($numberOfMonth)
    {
        $months = ['null', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return $months[$numberOfMonth];
    }

    /**
     * get list name month (Indonesia)
     * @return double
     */
    public static function getListMonth()
    {
        $months = [
            [ 'id' => '1', 'text' => Lang::get('January')],
            [ 'id' => '2', 'text' => Lang::get('February')],
            [ 'id' => '3', 'text' => Lang::get('March')],
            [ 'id' => '4', 'text' => Lang::get('April')],
            [ 'id' => '5', 'text' => Lang::get('May')],
            [ 'id' => '6', 'text' => Lang::get('June')],
            [ 'id' => '7', 'text' => Lang::get('July')],
            [ 'id' => '8', 'text' => Lang::get('August')],
            [ 'id' => '9', 'text' => Lang::get('September')],
            [ 'id' => '10', 'text' => Lang::get('October')],
            [ 'id' => '11', 'text' => Lang::get('November')],
            [ 'id' => '12', 'text' => Lang::get('Desember')]
        ];
        return $months;
    }

    /**
     * get list year now and back
     * @return double
     */
    public static function getListYear($count)
    {
        $years = [];
        $year = date('Y');
        for ($i=0; $i < $count; $i++) {
            $years[] = $year;
            $year--;
        }
        return $years;
    }

    /**
     * generate document number
     * @return string
     */
    public static function generateDocNumber($code, $table, $column, $lenght = 10)
    {
        $lenght_number = $lenght - strlen($code);

        $qLastId = DB::table($table)
                    ->orderByDesc($column)
                    ->select(DB::raw('RIGHT(' . $column . ', ' . $lenght_number . ') as id'))
                    ->whereRaw('LEFT('. $column . ', ' . strlen($code) .') = ?', [$code])
                    ->limit(1);


        if($qLastId->count() > 0){

            $lastId = $qLastId->first();

            $number = Helper::padLeft(intval($lastId->id) + 1, $lenght_number, '0');
        } else {
            $number = Helper::padLeft('1', $lenght_number, '0');
        }

        return $code . $number;
    }

    // utility
    /**
     * Pad both sides of a string with another.
     *
     * @param  string  $value
     * @param  int  $length
     * @param  string  $pad
     * @return string
     */
    public static function padBoth($value, $length, $pad = ' ')
    {
        return str_pad($value, $length, $pad, STR_PAD_BOTH);
    }

    /**
     * Pad the left side of a string with another.
     *
     * @param  string  $value
     * @param  int  $length
     * @param  string  $pad
     * @return string
     */
    public static function padLeft($value, $length, $pad = ' ')
    {
        return str_pad($value, $length, $pad, STR_PAD_LEFT);
    }

    /**
     * Pad the right side of a string with another.
     *
     * @param  string  $value
     * @param  int  $length
     * @param  string  $pad
     * @return string
     */
    public static function padRight($value, $length, $pad = ' ')
    {
        return str_pad($value, $length, $pad, STR_PAD_RIGHT);
    }
}
