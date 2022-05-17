<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class DashboardController extends Controller
{
    public function index(){
        $totalRegisteredUsers = DB::table('users')->count();
        // $totalProducts = DB::table('product')->count();

    	return view('Admin.dashboard')->with(compact('totalRegisteredUsers'));
    }
}