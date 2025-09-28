<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function dashboard()
    {
    	try {
    	    return view('layouts.app');
    	} catch(Exception $e) {
            // Log the error
             Log::error('Error in Dashboard: ', [
                 'message' => $e->getMessage(),
                 'code' => $e->getCode(),
                 'line' => $e->getLine(),
                 'trace' => $e->getTraceAsString()
             ]);
            return redirect()->route('login-admin');
        }
    }
}
