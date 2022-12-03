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

use App\Models\Tax\HistorySendTax;

class GenerateReportTax implements ShouldQueue
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
                case 'history-send-ftp':
                    $report = HistorySendTax::GenerateReport($download->filetype, $param);
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
