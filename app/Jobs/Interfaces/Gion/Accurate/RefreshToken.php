<?php

namespace App\Jobs\Interfaces\Gion\Accurate;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Configuration;

class RefreshToken implements ShouldQueue
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
        $url = config('qsrki.api.accurate.ouath_url');
        $client_id = Configuration::getValueByKeyFor('interface', 'client_id');
        $client_secret = Configuration::getValueByKeyFor('interface', 'client_secret');
        $refresh_token = Configuration::getValueByKeyFor('interface', 'refresh_token');

        $response = Http::asForm()
            ->withBasicAuth($client_id, $client_secret)
            ->post($url, [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refresh_token,
            ]);

        if ($response->ok()) {

            $res = $response->json();
            $access_token = $res['access_token'];
            $refresh_token = $res['refresh_token'];
            Configuration::setValueByKeyFor('interface', 'access_token', $access_token);
            Configuration::setValueByKeyFor('interface', 'refresh_token', $refresh_token);
        }
    }
}
