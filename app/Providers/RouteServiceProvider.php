<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->group(base_path('routes/auth.php'));

            Route::middleware('web', 'auth', 'verified')
                ->prefix('application')
                ->group(base_path('routes/modules/application.php'));

            Route::middleware('web', 'auth', 'verified')
                ->prefix('master')
                ->group(base_path('routes/modules/master.php'));

            Route::middleware('web', 'auth', 'verified')
                ->prefix('financeacc')
                ->group(base_path('routes/modules/financeacc.php'));

            Route::middleware('web', 'auth', 'verified')
                ->prefix('inventory')
                ->group(base_path('routes/modules/inventory.php'));

            Route::middleware('web', 'auth', 'verified')
                ->prefix('tax')
                ->group(base_path('routes/modules/tax.php'));

            Route::middleware('web', 'auth', 'verified')
                ->prefix('interfaces')
                ->group(base_path('routes/modules/interfaces.php'));

            Route::middleware('web', 'auth', 'verified')
                ->prefix('external-vendor')
                ->group(base_path('routes/modules/externalVendors.php'));

            Route::middleware('web', 'auth', 'verified')
                ->prefix('logbook')
                ->group(base_path('routes/modules/logbooks.php'));

            Route::middleware('web', 'auth', 'verified')
                ->prefix('report')
                ->group(base_path('routes/modules/report.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
