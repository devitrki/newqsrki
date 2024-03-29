<?php

namespace App\Jobs\ExternalVendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

use App\Jobs\ExternalVendor\SendTransactionVendor;

use App\Models\Company;

class ScheduleSendDailyExternalVendor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $companyId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $companyTimezone = Company::getConfigByKey($this->companyId, 'TIMEZONE');
        $date = Carbon::now($companyTimezone)->subDay()->format('Y/m/d');

        $sendVendors = DB::table('send_vendors')
                        ->where('company_id', $this->companyId)
                        ->where('status', 1)
                        ->select('id')
                        ->distinct()
                        ->get();

        foreach ($sendVendors as $sendVendor) {
            if (SendTransactionVendor::dispatch($date, $sendVendor->id)->onQueue('low')) {
                Log::info('Schedule send vendor date: ' . $date . ' and vendor id : ' . $sendVendor->id . ' running');
            } else {
                Log::error('Schedule send vendor date: ' . $date . ' and vendor id : ' . $sendVendor->id . ' not running');
            }
        }
    }
}
