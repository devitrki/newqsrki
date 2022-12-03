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

use App\Models\Financeacc\AssetMutation;
use App\Models\Financeacc\AssetRequestMutation;
use App\Models\Financeacc\AssetSo;

class GenerateReportFinanceacc implements ShouldQueue
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
                case 'outstanding-mutation-asset':
                    $report = AssetMutation::GenerateOutstandingReport($download->filetype, $param);
                    break;
                case 'log-mutation-asset':
                    $report = AssetMutation::GenerateLogReport($download->filetype, $param);
                    break;
                case 'asset-so':
                    $report = AssetSo::GenerateAssetSoReport($download->filetype, $param);
                    break;
                case 'selisih-asset-so':
                    $report = AssetSo::GenerateSelisihAssetSoReport($download->filetype, $param);
                    break;
                case 'outstanding-request-mutation':
                    $report = AssetRequestMutation::GenerateOutstandingReport($download->filetype, $param);
                    break;
                case 'log-request-mutation':
                    $report = AssetRequestMutation::GenerateLogReport($download->filetype, $param);
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
