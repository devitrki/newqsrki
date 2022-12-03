<?php

namespace App\Jobs\Interfaces\Aloha;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\Interfaces\Aloha\UploadSalesAloha;

class ScheduleSendSalesDaily implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $date = date('Y/m/d', strtotime('-1 days'));

        $plantPosAlohas = DB::connection('aloha')
                            ->table('dpvHstGndItem')
                            ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstGndItem.FKStoreId')
                            ->whereBetween('dpvHstGndItem.DateOfBusiness', [$date, $date])
                            ->select('gblStore.SecondaryStoreID' )
                            ->groupBy('gblStore.SecondaryStoreID')
                            ->get();

        foreach ($plantPosAlohas as $plant) {

            if (UploadSalesAloha::dispatch($plant->SecondaryStoreID, $date)->onQueue('low')) {
                Log::info('Send sales aloha daily date: ' . $date . ' and cust code : ' . $plant->SecondaryStoreID . ' running');
            } else {
                Log::error('Send sales aloha daily date: ' . $date . ' and cust code : ' . $plant->SecondaryStoreID . ' not running');
            }

        }
    }
}
