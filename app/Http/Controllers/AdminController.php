<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AdminController extends Controller
{
    public function login(Request $request){
        $role = $request->input('role');
        $email = $request->input('email');
        $password = $request->input('password');

        if($role == "admin"){
            $isUserExist = Admin::where('email', $email)->first();
            if($isUserExist){
                if($password == Crypt::decryptString($isUserExist->password)){
                    return response()->json(['data' => [
                        'login_permission' => 'granted',
                        'email' => $isUserExist['email'],
                        'username' => $isUserExist['username']
                    ]]);
                }
            }
        }else{
            $isUserExist = Mentor::where('email', $email)->first();
            if($isUserExist){
                if($password == Crypt::decryptString($isUserExist->password)){
                    return response()->json(['data' => [
                        'login_permission' => 'granted',
                        'email' => $isUserExist['email'],
                        'username' => $isUserExist['fullname'],
                        'token' => $isUserExist['id'],
                        'image' => $isUserExist['image']
                    ]]);
                }
            }
        }

        return response()->json(['data' => ['login_permission' => 'not granted']]);
    }
}
