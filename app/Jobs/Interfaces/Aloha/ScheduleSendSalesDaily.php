<?php

namespace App\Jobs\Interfaces\Aloha;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Jobs\Interfaces\Aloha\UploadSalesAloha;

use App\Models\Pos;
use App\Models\Company;

class ScheduleSendSalesDaily implements ShouldQueue
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

        $posData = DB::table('pos')
                ->where('company_id', $this->companyId)
                ->get();

        foreach ($posData as $pos) {
            $posRepository = Pos::getInstanceRepo($pos);
            $initConnectionAloha = $posRepository->initConnectionDB();
            if ($initConnectionAloha['status']) {
                $sendDailyStores = $posRepository->getListSendDailyStores($this->companyId, $date);

                foreach ($sendDailyStores as $sendDailyStore) {
                    if (UploadSalesAloha::dispatch($this->companyId, $sendDailyStore->SecondaryStoreID, $date)->onQueue('low')) {
                        Log::info('Send sales aloha daily date: ' . $date . ' and cust code : ' . $sendDailyStore->SecondaryStoreID . ' running');
                    } else {
                        Log::error('Send sales aloha daily date: ' . $date . ' and cust code : ' . $sendDailyStore->SecondaryStoreID . ' not running');
                    }
                }
            } else {
                Log::info('Send sales aloha daily date: ' . $date . ' failed: not connect aloha');
            }
        }
    }
}
