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

use App\Models\Logbook\LbDlyInvKitchen;
use App\Models\Logbook\LbDlyInvCashier;
use App\Models\Logbook\LbDlyInvWarehouse;
use App\Models\Logbook\LbStockCard;
use App\Models\Logbook\LbDlyWasted;
use App\Models\Logbook\LbRecMaterial;
use App\Models\Logbook\LbDlyClean;
use App\Models\Logbook\LbBriefing;
use App\Models\Logbook\LbDlyDuties;
use App\Models\Logbook\LbCleanDuties;
use App\Models\Logbook\LbWaterMeter;
use App\Models\Logbook\LbElectricMeter;
use App\Models\Logbook\LbGasMeter;
use App\Models\Logbook\LbEnvPump;
use App\Models\Logbook\LbEnvWater;
use App\Models\Logbook\LbEnvSolid;
use App\Models\Logbook\LbToilet;
use App\Models\Logbook\LbOrganoleptik;
use App\Models\Logbook\LbTemperature;
use App\Models\Logbook\LbMonSls;
use App\Models\Logbook\LbProdPlan;

class GenerateReportLogbook implements ShouldQueue
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
                case 'daily-inventory-kitchen':
                    $report = LbDlyInvKitchen::GenerateReport($download->filetype, $param);
                    break;
                case 'daily-inventory-cashier':
                    $report = LbDlyInvCashier::GenerateReport($download->filetype, $param);
                    break;
                case 'daily-inventory-warehouse':
                    $report = LbDlyInvWarehouse::GenerateReport($download->filetype, $param);
                    break;
                case 'stock-card':
                    $report = LbStockCard::GenerateReport($download->filetype, $param);
                    break;
                case 'daily-wasted':
                    $report = LbDlyWasted::GenerateReport($download->filetype, $param);
                    break;
                case 'reception-material':
                    $report = LbRecMaterial::GenerateReport($download->filetype, $param);
                    break;
                case 'daily-cleaning':
                    $report = LbDlyClean::GenerateReport($download->filetype, $param);
                    break;
                case 'duty-roster':
                    $report = LbBriefing::GenerateReport($download->filetype, $param);
                    break;
                case 'daily-duties':
                    $report = LbDlyDuties::GenerateReport($download->filetype, $param);
                    break;
                case 'cleaning-duties':
                    $report = LbCleanDuties::GenerateReport($download->filetype, $param);
                    break;
                case 'water-meter':
                    $report = LbWaterMeter::GenerateReport($download->filetype, $param);
                    break;
                case 'electric-meter':
                    $report = LbElectricMeter::GenerateReport($download->filetype, $param);
                    break;
                case 'gas-meter':
                    $report = LbGasMeter::GenerateReport($download->filetype, $param);
                    break;
                case 'env-propump':
                    $report = LbEnvPump::GenerateReport($download->filetype, $param);
                    break;
                case 'env-wastewater':
                    $report = LbEnvWater::GenerateReport($download->filetype, $param);
                    break;
                case 'env-solidwaste':
                    $report = LbEnvSolid::GenerateReport($download->filetype, $param);
                    break;
                case 'temperature':
                    $report = LbTemperature::GenerateReport($download->filetype, $param);
                    break;
                case 'toilet':
                    $report = LbToilet::GenerateReport($download->filetype, $param);
                    break;
                case 'organoleptik':
                    $report = LbOrganoleptik::GenerateReport($download->filetype, $param);
                    break;
                case 'money-sales':
                    $report = LbMonSls::GenerateReport($download->filetype, $param);
                    break;
                case 'production-planning':
                    $report = LbProdPlan::GenerateReport($download->filetype, $param);
                    break;
            }

            $status = 'Done';
            if (empty($report)) {
                $status = 'Error';
                Log::alert("Generate Report Error : id download " . $this->download_id . " error");
            }

            // after proses, update download to finish
            DB::table('downloads')
                ->where("id", $this->download_id)
                ->update([
                    'path' => $report['path'],
                    'filename' => $report['filename'],
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
