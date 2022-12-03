<?php

namespace App\Jobs\Tax;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\Tax\SendTaxFtp;

class ScheduleSendTaxFtp implements ShouldQueue
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
        $sendTaxes = DB::table('send_taxes')
                    ->where('status', 1)
                    ->select('id')
                    ->distinct()
                    ->get();

        $date = date('Y/m/d',strtotime("-1 days"));

        foreach ($sendTaxes as $tax) {
            if (SendTaxFtp::dispatch($date, $tax->id)->onQueue('low')) {
                Log::info('Schedule send tax ftp date: ' . $date . ' and tax id : ' . $tax->id . ' running');
            } else {
                Log::error('Schedule send tax ftp date: ' . $date . ' and tax id : ' . $tax->id . ' not running');
            }
        }
    }
}
