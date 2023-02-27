<?php

namespace App\Models\ExternalVendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

class HistorySendVendor extends Model
{
    use HasFactory;

    public static function addHistoryFailed($companyId, $date, $sendVendorId, $desc)
    {
        $historySendVendor = new HistorySendVendor;
        $historySendVendor->company_id = $companyId;
        $historySendVendor->date = $date;
        $historySendVendor->send_vendor_id = $sendVendorId;
        $historySendVendor->status = 0;
        $historySendVendor->description = Lang::get($desc);
        $historySendVendor->save();
    }
}
