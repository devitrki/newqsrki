<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\User;

class CompanyController extends Controller
{
    public function changeCompanySelectedUser(Request $request){
        $user = User::find($request->user_id);
        $user->company_id = $request->company_id;
        if($user->save()){
            Cache::forget('profile_by_user_id_' . $user->id);
            Cache::forget('company_by_user_id_' . $user->id);
            $status = true;
        } else {
            $status = false;
        }

        return response()->json([
                                    'status' => $status,
                                    'message' => "",
                                    'data' => []
                                ]);
    }
}
