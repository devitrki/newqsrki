<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Library\Helper;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function relogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $stat = "failed";
        $token = "";
        if (Auth::attempt($credentials)) {
            $token = csrf_token();
            $stat = "success";
        }
        return response()->json( Helper::resJSON( $stat, "", ["token" => $token] ) );
    }


}
