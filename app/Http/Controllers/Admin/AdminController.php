<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Utilities\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //
    // public function index(Request $request)
    // {
    //     return view('Admin.login');
    // }

    public function getLogin()
    {
        return view('Admin/login');
    }
    public function postLogin(LoginRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'level' => [Constant::user_level_host, Constant::user_level_admin], // tk host hoac admin
        ];

        $remember = $request->remember;
        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            //            return redirect('');
            // dd($credentials);
            return redirect('/quantri/dashboard');
        } else {
            return redirect()->back()->withErrors("Tài khoản không hợp lệ");
        }
    }

    public function logout()
    {
        // $request->session()->forget("email");
        Auth::logout();
        return redirect("/quantri/login");
    }
}
