<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index()
    {
        return redirect()->route('user-index');
    }

    public function loginPage(Request $request)
    {
        if ($request->session()->has('api_token') && $request->session()->has('user')) {
            return redirect()->route('dashboard');
        }
    	return view('admin_login');
    }
}
