<?php

namespace App\Http\Controllers\Other;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;

class CollectController extends Controller
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
        return view('collect.index');
    }

    public function message(Request $request)
    {
        try{
            $result = DB::table('collect')->insert($request->all());
        }catch (\Exception $e){
            return json_encode(['code'=>500,'msg'=>$e->getMessage()]);
        }

        if($result){
            return json_encode(['code'=>200]);
        }
        return json_encode(['code'=>500,'msg'=>'保存失败']);

    }
}
