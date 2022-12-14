<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// flag change pass
use App\Jobs\ScheduleFlagChangeMonthlyPass;

// tax
use App\Jobs\Tax\ScheduleSendTaxFtp;

// asset so
use App\Jobs\Financeacc\GenerateAssetSO;
use App\Jobs\Financeacc\SubmitAssetSO;

use App\Models\Configuration;
use App\Models\Company;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new ScheduleFlagChangeMonthlyPass)->monthly();

        $companies = DB::table('companies')
                        ->select('id')
                        ->get();

        foreach ($companies as $company) {
            $companyTimezone = Company::getConfigByKey($company->id, 'TIMEZONE');
            if (!$companyTimezone) {
                continue;
            }

            // schedule send tax
            if ($companyTimezone) {
                $schedule->job(new ScheduleSendTaxFtp($company->id))
                    ->timezone($companyTimezone)
                    ->dailyAt('23:26');
            }

            // schedule for asset so
            $statusGenerateAssetSo = Configuration::getValueCompByKeyFor($company->id, 'financeacc', 'status_generate_asset_so');

            if ($statusGenerateAssetSo == 'true') {
                $dateGenerateOutletAssetSo = Configuration::getValueCompByKeyFor($company->id, 'financeacc', 'date_generate_outlet_asset_so');
                $dateSubmitOutletAssetSo = Configuration::getValueCompByKeyFor($company->id, 'financeacc', 'date_submit_outlet_asset_so');
                $dateGenerateDcAssetSo = Configuration::getValueCompByKeyFor($company->id, 'financeacc', 'date_generate_dc_asset_so');
                $dateSubmitDcAssetSo = Configuration::getValueCompByKeyFor($company->id, 'financeacc', 'date_submit_dc_asset_so');

                // generate
                if($dateGenerateOutletAssetSo){
                    $schedule
                        ->job(new GenerateAssetSO($company->id, 'outlet'))
                        ->timezone($companyTimezone)
                        ->cron('*/2 * ' . $dateGenerateOutletAssetSo . ' * *');
                }
                if($dateGenerateDcAssetSo){
                    $schedule
                        ->job(new GenerateAssetSO($company->id, 'dc'))
                        ->timezone($companyTimezone)
                        ->cron('*/6 * ' . $dateGenerateDcAssetSo . ' * *');
                }

                // submit
                if($dateSubmitOutletAssetSo){
                    $schedule
                        ->job(new SubmitAssetSO($company->id, 'outlet'))
                        ->timezone($companyTimezone)
                        ->cron('*/2 * ' . $dateSubmitOutletAssetSo . ' * *');
                }
                if($dateSubmitDcAssetSo){
                    $schedule
                        ->job(new SubmitAssetSO($company->id, 'dc'))
                        ->timezone($companyTimezone)
                        ->cron('*/6 * ' . $dateSubmitDcAssetSo . ' * *');
                }
            }
        }

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
