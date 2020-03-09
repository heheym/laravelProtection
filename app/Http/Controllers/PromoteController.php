<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PromoteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $createdDate= DB::table('promote')->orderBy('created_date','desc')->select('id','created_date')->limit(10)->get();
        return view('promote')->with(['createdDate'=>$createdDate,'url'=>url('promote/song')]);
    }

    public function song()
    {
        $get = $_GET['index'];
        $data = DB::table('promote')->where('id',$get)->first();
        return response()->json(['code' => 200, 'msg' => '请求成功','data'=>$data]);
    }
}
