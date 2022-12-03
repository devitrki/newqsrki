<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use App\Models\Auth\Menu;
use App\Models\Company;

use App\Mail\Financeacc\NotificationPettycash;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $userAuth = $request->get('userAuth');
        $userMappingMenus = Menu::getMappingMenuByUserId(Auth::id());
        $companiesUser = Company::getCompaniesUser($userAuth);

        return view('home', [
                                'user' => $userAuth,
                                'user_mapping_menus' => $userMappingMenus,
                                'user_companies' => $companiesUser
                            ]
                    );
    }
}
