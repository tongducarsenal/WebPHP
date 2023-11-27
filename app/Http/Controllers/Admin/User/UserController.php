<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AddUserRequest;
use App\Http\Requests\User\EditUserRequest;
use App\Models\User;
use App\Slug\Slug;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        $perPage = 5; // Số lượng mục trên mỗi trang
        $currentPage = request()->query('page', 1); // Lấy trang hiện tại từ query string, mặc định là trang 1

        // Lấy tổng số lượng người dùng trong cơ sở dữ liệu
        $totalUsers = User::count();

        // Tính toán vị trí bắt đầu và kết thúc của kết quả trên trang hiện tại
        $startResult = ($currentPage - 1) * $perPage + 1;
        $endResult = min($startResult + $perPage - 1, $totalUsers);

        // Lấy danh sách người dùng với phân trang
        $users = User::orderBy("id", "asc");

        // Kiểm tra xem có dữ liệu tìm kiếm được gửi lên không
        if ($request->has('search')) {
            $search = $request->input('search');
            $users->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $users->paginate($perPage);

        // Truyền các giá trị vào view
        return view('Admin.user.index', [
            "users" => $users,
            "startResult" => $startResult,
            "endResult" => $endResult,
            "totalResults" => $totalUsers,
        ]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('Admin.user.user-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddUserRequest $request)
    {
        // Kiểm tra xem có tệp hình ảnh được tải lên không
        if ($request->hasFile("image")) {
            $slug = Slug::getSlug($request->name);
            $file = $request->image;

            // Lấy phần mở rộng của tệp
            $fileExtension = $file->getClientOriginalExtension();

            // Tạo tên tệp sử dụng slug và phần mở rộng
            $filename = $slug . "." . $fileExtension;

            // Di chuyển tệp đã tải lên vào thư mục "admin/assets/images/user" với tên tệp được tạo ra
            $file->move("admin/assets/images/user", $filename);

            // Tạo một đối tượng User và đặt các giá trị
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->level = $request->level;
            $user->password = bcrypt($request->password);
            $user->description = $request->description;
            $user->company_name = $request->company_name;
            $user->country = $request->country;
            $user->street_address = $request->street_address;
            $user->postcode_zip = $request->postcode_zip;
            $user->town_city = $request->town_city;
            $user->phone = $request->phone;
            $user->avatar = $filename;

            // Lưu đối tượng User vào cơ sở dữ liệu
            $user->save();

            // Chuyển hướng về trang danh sách người dùng sau khi lưu thành công
            return redirect("/quantri/user")->with("alert", "Đã thêm thành công");
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //
        $id = $request->id;
        $user = User::find($id)->toArray();
        // dd($user);
        return view('Admin.user.user-show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        //
        $id = $request->id;
        $user = User::find($id)->toArray();
        return view('Admin.user.user-edit', ["user" => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditUserRequest $request)
    {
        //
        $id = $request->id;
        $slug = Slug::getSlug($request->name);
        $user = User::find($id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->level = $request->level;
        $user->password = bcrypt($request->password);
        $user->description = $request->description;
        $user->company_name = $request->company_name;
        $user->country = $request->country;
        $user->street_address = $request->street_address;
        $user->postcode_zip = $request->postcode_zip;
        $user->town_city = $request->town_city;
        $user->phone = $request->phone;

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
        return redirect("/quantri/user")->with("alert", "Đã sửa thành công");
        // return view('Admin.user.user-edit');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        // detele
        $id = $request->id;
        $user = User::find($id);
        $file_name = $user->avatar;
        if ($file_name != '') {
            unlink('admin/assets/images/user/' . $file_name);
        }
        $user->delete();
        // dd($user);
        return redirect("/quantri/user")->with("alert", "Đã xóa thành công");
    }
}
