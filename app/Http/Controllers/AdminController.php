<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function login(Request $request){
        $role = $request->input('role');
        $email = $request->input('email');
        $password = $request->input('password');

        if($role == "admin"){
            $isUserExist = Admin::where('email', $email)->where('password', $password)->first();
            if($isUserExist){
                return response()->json(['data' => [
                    'login_permission' => 'granted',
                    'email' => $isUserExist['email'],
                    'username' => $isUserExist['username']
                ]]);
            }else{
                return response()->json(['data' => ['login_permission' => 'not granted']]);
            }
        }

        return response()->json(['data' => ['login_permission' => 'not granted']]);
    }
}
