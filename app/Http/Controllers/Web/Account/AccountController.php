<?php

namespace App\Http\Controllers\Web\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\AccountRequest;
use App\Http\Requests\Account\EditAccountRequest;
use App\Http\Requests\Account\EditChangePassRequest;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use App\Slug\Slug;
use App\Utilities\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    //
    public function login()
    {
        $categories = Category::all();
        return view('FrontEnd.account.login', ['categories' => $categories]);
    }

    public function checkLogin(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'level' => 2, // Tài khoản cấp độ khách hàng bình thường
        ];

        $remember = $request->remember;
        if (Auth::attempt($credentials, $remember)) {
            return redirect('/'); // trang chủ
            // return redirect()->intended('');
        } else {
            return back()->with("alert", "Email or mật khẩu bị nhập sai");
        }
    }

    public function logout()
    {
        Auth::logout();

        return back();
    }

    public function register()
    {
        $categories = Category::all();
        return view('FrontEnd.account.register', ['categories' => $categories]);
    }

    public function postRegister(AccountRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->level = 2;  // Cấp độ khách hàng bình thường
        $user->password = bcrypt($request->password);
        $user->save();

        // Đăng nhập ngay sau khi đăng ký (tùy chọn)
        // Auth::login($user);

        return redirect('account/login')->with('noti', 'Đăng ký thành công ! Vui lòng đăng nhập');
    }

    public function myOrderIndex()
    {
        $categories = Category::all();
        $user_id = Auth::id();
        $orders = Order::with('orderDetails')->where('user_id', $user_id)->get();
        // dd(Auth::id());
        // dd($orders);
        return view('FrontEnd.account.my-order.index', ['categories' => $categories, 'orders' => $orders]);
    }
    public function myOrderShow($id)
    {
        $categories = Category::all();
        $order = Order::with('orderDetails')->find($id);
        // dd($order);
        return view('FrontEnd.account.my-order.show', ['categories' => $categories, 'order' => $order]);
    }

    public function myOrderCancel(Request $request)
    {
        $id = $request->id;
        $order = Order::find($id);
        $order->status = Constant::order_status_Cancel;
        $order->save();
        dd($order->orderDetails);
    }

    public function profile()
    {
        $user = User::find(Auth::id());
        $categories = Category::all();
        return view('FrontEnd.account.profile.index', ['user' => $user, 'categories' => $categories]);
    }
    public function editProfile(EditAccountRequest $request)
    {
        $user = User::find(Auth::id());

        $slug = Slug::getSlug($request->name);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile("image")) {
            $file = $request->image;

            // Get the file extension
            $fileExtension = $file->getClientOriginalExtension();

            // Generate the filename using the slug and extension
            $filename = $slug . "." . $fileExtension;

            // Move the uploaded file to the "uploads" directory with the generated filename
            $file->move("admin/assets/images/user", $filename);
            $user->avatar = $filename;
        }

        // Lưu đối tượng User vào cơ sở dữ liệu
        $user->save();

        // Chuyển hướng về trang danh sách người dùng sau khi lưu thành công
        return redirect("/account/profile")->with("alert", "Đã sửa thành công");
        // return view('FrontEnd..index', ['user' => $user, 'categories' => $categories]);
    }

    public function changePass()
    {
        $user = User::find(Auth::id());
        $categories = Category::all();
        return view('FrontEnd.account.changepass.index', ['user' => $user, 'categories' => $categories]);
    }
    public function editchangePass(EditChangePassRequest $request)
    {
        $user = User::find(Auth::id());
        if (!Hash::check($request->get('password'), $user->password)) {
            return back()->with('alert', 'ERROR: Bạn nhập sai mật khẩu cũ');
        }
        if ($request->get('new_password') != $request->get('cf_password')) {
            return back()->with('alert', 'ERROR: Xác nhận mật khẩu không khớp');
        }

        $newPassword = bcrypt($request->get('new_password'));
        $user->password = $newPassword;
        $user->save();
        $categories = Category::all();
        return view('FrontEnd.account.changepass.index', ['user' => $user, 'categories' => $categories]);
        // dd('duogbachdev');
    }
}
