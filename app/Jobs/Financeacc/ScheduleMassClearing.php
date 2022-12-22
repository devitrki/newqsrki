<?php

namespace App\Jobs\Financeacc;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;
use App\Jobs\Financeacc\GenerateMassClearing;
use App\Models\Financeacc\MassClearing;

class ScheduleMassClearing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // update status process mass clearing start
        $massClearing = MassClearing::find($this->id);
        $massClearing->time_process_start = \Carbon\Carbon::now();
        $massClearing->status_generate = 1;
        $massClearing->save();

        $massClearingDetails = DB::table('mass_clearing_details')
                            ->where('mass_clearing_id', $this->id)
                            ->select(
                                'mass_clearing_id',
                                'bank_in_bank_gl',
                                'bank_in_date',
                                'sales_date',
                                'sales_month',
                                'sales_year',
                                'special_gl',
                                'plant_id',
                                DB::raw('SUM(bank_in_nominal) as bank_in_nominal, SUM(bank_in_charge) as bank_in_charge, COUNT(id) as total_row')
                            )
                            ->groupBy('mass_clearing_id', 'bank_in_bank_gl', 'bank_in_date', 'sales_date', 'sales_month', 'sales_year', 'special_gl', 'plant_id')
                            ->get();

        foreach ($massClearingDetails as $massClearingDetail) {
            GenerateMassClearing::dispatch($massClearing->company_id, $massClearingDetail)->onQueue('massclearing');
        }

    }
}
