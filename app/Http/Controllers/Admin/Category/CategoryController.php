<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\AddCategoryRequest;
use App\Http\Requests\Category\EditCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $perPage = 5; // Số lượng mục trên mỗi trang
        $currentPage = request()->query('page', 1); // Lấy trang hiện tại từ query string, mặc định là trang 1

        // Lấy tổng số lượng người dùng trong cơ sở dữ liệu
        $totalUsers = Category::count();

        // Tính toán vị trí bắt đầu và kết thúc của kết quả trên trang hiện tại
        $startResult = ($currentPage - 1) * $perPage + 1;
        $endResult = min($startResult + $perPage - 1, $totalUsers);

        // Lấy danh sách người dùng với phân trang
        $categories = Category::orderBy("id", "asc");

        // Kiểm tra xem có dữ liệu tìm kiếm được gửi lên không
        if ($request->has('search')) {
            $search = $request->input('search');
            $categories->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }

        $categories = $categories->paginate($perPage);

        // Truyền các giá trị vào view
        return view('Admin.category.category', [
            "categories" => $categories,
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
        return view('Admin.category.category-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddCategoryRequest $request)
    {
        //
        $category = new Category();
        $category->name = $request->name;
        $category->save();
        return redirect("/quantri/category")->with("alert", "Đã thêm thành công");
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
        return view('Admin.category.category');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        //
        $id = $request->id;
        $category = Category::find($id)->toArray();
        return view('Admin.category.category-edit', ["category" => $category]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditCategoryRequest $request)
    {
        //
        $id = $request->id;
        $category = Category::find($id);

        $category->name = $request->name;
        $category->save();
        return redirect("/quantri/category")->with("alert", "Đã sửa thành công");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        // detele
        $id = $request->id;
        $category = Category::find($id);
        $category->delete();
        // dd($category);
        return redirect("/quantri/category")->with("alert", "Đã xóa thành công");
    }
}
