<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class DownloadController extends Controller
{
    public function index(Request $request)
    {
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('application.download', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('downloads')
                    ->select(['id', 'name', 'status', 'filetype', 'created_at', 'module'])
                    ->where('company_id', $userAuth->company_id_selected)
                    ->where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc');

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('date', function ($data) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at);
                    return $created_at->format('d/m/Y H:i:s');
                })
                ->make();
    }

    public function download($id)
    {
        $download = DB::table('downloads')->where('id', $id)->first();
        $pathfile = storage_path('app/public/'.$download->path.$download->filename);
        return response()->download($pathfile);
    }
}
