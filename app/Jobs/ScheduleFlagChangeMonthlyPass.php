<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ScheduleFlagChangeMonthlyPass implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = DB::table('users')->select('id')->where('status', '=', 2)->get();
        foreach ($users as $duser) {
            $user = User::find($duser->id);
            $user->flag_change_pass = 1;
            if (!$user->save()) {
                Log::alert('Change flag password monthly failed with id ' . $duser->id);
            }
        }
    }
}
