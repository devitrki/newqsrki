<?php

namespace App\Jobs\Tax;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

use App\Jobs\Tax\SendTaxFtp;

use App\Models\Company;

class ScheduleSendTaxFtp implements ShouldQueue
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

        $sendTaxes = DB::table('send_taxes')
                        ->where('company_id', $this->companyId)
                        ->where('status', 1)
                        ->select('id')
                        ->distinct()
                        ->get();

        foreach ($sendTaxes as $tax) {
            if (SendTaxFtp::dispatch($date, $tax->id)->onQueue('low')) {
                Log::info('Schedule send tax ftp date: ' . $date . ' and tax id : ' . $tax->id . ' running');
            } else {
                Log::error('Schedule send tax ftp date: ' . $date . ' and tax id : ' . $tax->id . ' not running');
            }
        }
    }
}
