<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AccessController extends Controller
{
    public function adminLogin(Request $request)
    {
    	try
        {
        	$data = $request->all();
		    	if(Auth::attempt(['username' => $data['username'], 'password' => $data['password']])){

		    		$notification = array(
		                     'message' => 'Successfully Logged In',
		                     'alert-type' => 'success'
		                    );

		           return redirect('/dashboard')->with($notification);
		    	} else {
		    		$notification = array(
		                     'message' => 'Username or Password Invalid',
		                     'alert-type' => 'error'
		                    );

		          return Redirect()->back()->with($notification);
	    	}
	   } catch(Exception $e){
            // Log the error
            Log::error('Error in Login: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            $notification=array(
                'message' => 'Something went wrong!!!',
                'alert-type' => 'error'
            );
            return Redirect()->back()->with($notification);
        }
    }

    public function logout(Request $request)
    {
        try {
            $token = $request->session()->get('api_token');

            if ($token) {
                // Call your API logout
                $apiBaseUrl = config('app.api_base_url');
                $response = Http::withToken($token)->post($apiBaseUrl . 'api/v1/logout');
            }

            // Clear session
            $request->session()->forget(['api_token', 'user']);
            $request->session()->flush();

            return redirect()->route('login-admin')->with('message', 'Logged out successfully.');
        } catch (Exception $e) {
            Log::error('Error during logout: ', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            // Always clear session to be safe
            $request->session()->forget(['api_token', 'user']);
            $request->session()->flush();

            return redirect()->route('login-admin')->with('message', 'Logout failed, but you are logged out locally.');
        }
    }

    public function storeApiToken(Request $request)
    {
        try {
            $request->session()->put('api_token', $request->token);
            $request->session()->put('user', $request->user);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Error in storeApiToken: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['success' => false, 'message' => 'Something went wrong!!!'], 500);
        }
    }
}
