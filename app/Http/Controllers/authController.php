<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if ($request->method() == 'GET'){
            return view('user/login');
        }
        $user = User::query()->where('username',$request->username)->get();
        if ($user->first()){
            if(Hash::check($request->password,$user->first()->password)){
                Session::put('isLogin',1);
                return view('index');

            }
        }
        return back();
    }
}
