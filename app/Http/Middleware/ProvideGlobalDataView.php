<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Library\Helper;

use App\Models\Configuration;
use App\Models\Profile;

class ProvideGlobalDataView
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        View::share('dom', Helper::generateDOM());
        View::share('configurationwebs', Configuration::getConfigurationByFor('web'));

        if (Auth::check()) {
            $userAuth = Profile::getProfileByUserId(Auth::id());
            $request->attributes->add(['userAuth' => $userAuth]);
        }

        return $next($request);
    }
}
