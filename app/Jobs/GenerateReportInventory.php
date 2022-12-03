<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Throwable;

use App\Models\Inventory\GiPlant;
use App\Models\Inventory\GrPlant;
use App\Models\Inventory\GrVendor;
use App\Models\Inventory\Waste;
use App\Models\Inventory\Posto;
use App\Models\Stock;

use App\Models\Inventory\Usedoil\UoSaldoVendorHistory;
use App\Models\Inventory\Usedoil\UoMovement;
use App\Models\Inventory\Usedoil\UoMovementItem;

class GenerateReportInventory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $download_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($download_id)
    {
        $this->download_id = $download_id;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // get data dowonload
        $download = DB::table('downloads')->where("id", $this->download_id)->first();
        if ($download) {
            // before proses, update download to proses
            DB::table('downloads')
            ->where("id", $this->download_id)
                ->update(['status' => 'Process']);

            $param = json_decode($download->param);

            // proses generate call to model
            $report = [];
            switch ($download->type) {
                case 'gi-plant':
                    $report = GiPlant::GenerateReport($download->filetype, $param);
                    break;
                case 'gr-plant':
                    $report = GrPlant::GenerateReport($download->filetype, $param);
                    break;
                case 'gr-vendor':
                    $report = GrVendor::GenerateReport($download->filetype, $param);
                    break;
                case 'waste':
                    $report = Waste::GenerateReport($download->filetype, $param);
                    break;
                case 'current-stock':
                    $report = Stock::GenerateReport($download->filetype, $param);
                    break;
                case 'outstanding-posto':
                    $report = Posto::GenerateReport($download->filetype, $param);
                    break;

                // usedoil
                case 'uo-history-saldo-vendor':
                    $report = UoSaldoVendorHistory::GenerateReport($download->filetype, $param);
                    break;
                case 'uo-income-sales-detail':
                    $report = UoMovementItem::GenerateReport($download->filetype, $param);
                    break;
                case 'uo-income-sales-summary':
                    $report = UoMovement::GenerateReport($download->filetype, $param);
                    break;
            }

            $status = 'Done';
            if (empty($report)) {
                $status = 'Error';
                $path = '';
                $filename = '';
                Log::alert("Generate Report Error : id download " . $this->download_id . " error");
            } else {
                $status = 'Done';
                $path = $report['path'];
                $filename = $report['filename'];
            }

            // after proses, update download to finish
            DB::table('downloads')
                ->where("id", $this->download_id)
                ->update([
                    'path' => $path,
                    'filename' => $filename,
                    'status' => $status,
                ]);

        } else {
            // print out to log if error
            Log::alert("Generate Report Error : id download " . $this->download_id . " not found");
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        // update table if error
        DB::table('downloads')
                ->where("id", $this->download_id)
                ->update([
                    'status' => 'Failed',
                ]);
    }
}
