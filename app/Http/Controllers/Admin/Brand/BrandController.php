<?php

namespace App\Http\Controllers\Admin\Brand;

use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\AddBrandRequest;
use App\Http\Requests\Brand\EditBrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
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
        $totalUsers = Brand::count();

        // Tính toán vị trí bắt đầu và kết thúc của kết quả trên trang hiện tại
        $startResult = ($currentPage - 1) * $perPage + 1;
        $endResult = min($startResult + $perPage - 1, $totalUsers);

        // Lấy danh sách người dùng với phân trang
        $brands = Brand::orderBy("id", "asc");

        // Kiểm tra xem có dữ liệu tìm kiếm được gửi lên không
        if ($request->has('search')) {
            $search = $request->input('search');
            $brands->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }

        $brands = $brands->paginate($perPage);

        // Truyền các giá trị vào view
        return view('Admin.brand.brand', [
            "brands" => $brands,
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
        return view('Admin.brand.brand-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddBrandRequest $request)
    {
        //
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->save();
        return redirect("/quantri/brand")->with("alert", "Đã thêm thành công");
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        //
        return view('Admin.brand.brand');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        //
        $id = $request->id;
        $brand = Brand::find($id)->toArray();
        return view('Admin.brand.brand-edit', ["brand" => $brand]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditBrandRequest $request)
    {
        //
        $id = $request->id;
        $brand = Brand::find($id);

        $brand->name = $request->name;
        $brand->save();
        return redirect("/quantri/brand")->with("alert", "Đã sửa thành công");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
        $id = $request->id;
        $brand = Brand::find($id);
        $brand->delete();
        // dd($brand);
        return redirect("/quantri/brand")->with("alert", "Đã xóa thành công");
    }
}
