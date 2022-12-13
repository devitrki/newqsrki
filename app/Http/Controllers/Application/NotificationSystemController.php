<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\User;
use App\Models\NotificationSystem;
use App\Models\NotificationSystemContent;
use App\Models\NotificationSystemRead;

class NotificationSystemController extends Controller
{
    public function index(Request $request)
    {
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('application.notification-system', $dataview)->render();
    }

    public function dtble()
    {
        $query = DB::table('notification_systems')->select(['id', 'name', 'key', 'description']);
        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function dtbleContent($id)
    {
        $query = DB::table('notification_system_contents')
                    ->leftJoin('languanges', 'languanges.id', 'notification_system_contents.languange_id')
                    ->select(['languanges.lang', 'notification_system_contents.title', 'notification_system_contents.id',
                              'notification_system_contents.languange_id', 'notification_system_contents.content'])
                    ->where('notification_system_id', '=', $id);

        return Datatables::of($query)
                    ->addIndexColumn()
                    ->rawColumns(['content'])
                    ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'key' => 'required|unique:notification_systems,key',
            'description' => 'required',
        ]);

        $notificationSystem = new NotificationSystem;
        $notificationSystem->name = $request->name;
        $notificationSystem->key = $request->key;
        $notificationSystem->description = $request->description;
        if ($notificationSystem->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("notification system")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("notification system")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function storeContent(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'language' => 'required',
            'content' => 'required',
        ]);

        $notificationSystemContent = new NotificationSystemContent;
        $notificationSystemContent->notification_system_id = $request->notification_system_id;
        $notificationSystemContent->title = $request->title;
        $notificationSystemContent->languange_id = $request->language;
        $notificationSystemContent->content = $request->content;
        if ($notificationSystemContent->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("notification system content")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("notification system content")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'key' => 'required',
            'description' => 'required',
        ]);

        $notificationSystem = NotificationSystem::find($request->id);
        $notificationSystem->name = $request->name;
        $notificationSystem->key = $request->key;
        $notificationSystem->description = $request->description;
        if ($notificationSystem->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("notification system")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("notification system")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function updateContent(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'language' => 'required',
            'content' => 'required',
        ]);

        $notificationSystemContent = NotificationSystemContent::find($request->id);
        $notificationSystemContent->title = $request->title;
        $notificationSystemContent->languange_id = $request->language;
        $notificationSystemContent->content = $request->content;
        if ($notificationSystemContent->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("notification system content")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("notification system content")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function destroy($id)
    {
        $notificationSystem = NotificationSystem::find($id);
        $notificationSystemContent = NotificationSystemContent::where('notification_system_id', '=', $notificationSystem->id);

        $stat = 'success';
        if($notificationSystemContent->count() > 0){
            if (!$notificationSystemContent->delete()) {
                $stat = 'failed';
            }
        }

        if (!$notificationSystem->delete()) {
            $stat = 'failed';
        }

        if ($stat != 'failed') {
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("notification system")]);
        } else {
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("notification system")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function destroyContent($id)
    {
        $notificationSystemContent = NotificationSystemContent::find($id);
        if ($notificationSystemContent->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("notification system content")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("notification system content")]);
        }
        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function send(Request $request)
    {
        $request->validate([
            'role' => 'required',
        ]);

        $roles = explode(',', $request->role);

        $users = User::whereHas("roles", function ($q) use ($roles) {
                        $q->whereIn('id', $roles);
                    })
                    ->select('id')
                    ->get();
        $suc = true;
        foreach ($users as $user) {
            $notificationSystemRead = new NotificationSystemRead;
            $notificationSystemRead->notification_system_id = $request->id;
            $notificationSystemRead->user_id = $user->id;
            $notificationSystemRead->read = 0;
            if(!$notificationSystemRead->save()){
                $suc = false;
                break;
            }
        }

        if ($suc) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("send notification")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("send notification")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function getNotificationUser()
    {
        $user_id = Auth::id();
        $notif = DB::table('notification_system_reads as r')
                    ->leftJoin('notification_system_contents as c', 'c.notification_system_id', 'r.notification_system_id')
                    ->select('r.id', 'c.title', 'c.content')
                    ->where('r.user_id', $user_id)
                    ->where('r.read', 0)
                    ->where('c.languange_id', User::getLanguageByUserId($user_id))
                    ->first();

        return response()->json(Helper::resJSON('success', '', $notif));
    }

    public function readNotificationUser($id)
    {
        $notificationSystemRead = NotificationSystemRead::find($id);
        $notificationSystemRead->read = 1;
        if ($notificationSystemRead->save()) {
            $stat = 'success';
        }else{
            $stat = 'failed';
        }

        return response()->json(Helper::resJSON($stat, ''));
    }
}
