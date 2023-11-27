<?php

namespace App\Http\Controllers\Admin\Slider;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
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
        $totalSliders = Slider::count();

        // Tính toán vị trí bắt đầu và kết thúc của kết quả trên trang hiện tại
        $startResult = ($currentPage - 1) * $perPage + 1;
        $endResult = min($startResult + $perPage - 1, $totalSliders);

        // Lấy danh sách người dùng với phân trang
        $sliders = Slider::orderBy("id", "asc");

        // Kiểm tra xem có dữ liệu tìm kiếm được gửi lên không
        if ($request->has('search')) {
            $search = $request->input('search');
            $sliders->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $sliders = $sliders->paginate($perPage);
        return view('Admin.slider.index', [
            'sliders' => $sliders,
            "startResult" => $startResult,
            "endResult" => $endResult,
            "totalResults" => $totalSliders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('Admin.slider.slider-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        if ($request->hasFile("image")) {
            $file = $request->image;

            // Get the file extension
            $fileExtension = $file->getClientOriginalExtension();

            // Generate the filename using the slug and extension
            $filename = "slider-" . time() . "." . $fileExtension;

            // Move the uploaded file to the "uploads" directory with the generated filename
            $file->move("admin/assets/images/sliders", $filename);

            $slider = new Slider();
            $slider->path = $filename;

            $slider->save();

            return redirect("/quantri/slider")->with("alert", "Đã thêm thành công");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        //
        $id = $request->id;
        $slider = Slider::find($id);
        return view('Admin.slider.slider-edit', ['slider' => $slider]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //
        $id = $request->id;
        $slider = Slider::find($id);

        if ($request->hasFile("image")) {
            $file = $request->image;

            // Get the file extension
            $fileExtension = $file->getClientOriginalExtension();

            // Generate the filename using the slug and extension
            $filename = "slider-" . $id . "." . $fileExtension;

            // Move the uploaded file to the "uploads" directory with the generated filename
            $file->move("admin/assets/images/sliders", $filename);
            $slider->path = $filename;
        }

        // Lưu đối tượng User vào cơ sở dữ liệu
        $slider->save();
        // dd($filename);
        return redirect("/quantri/slider")->with("alert", "Đã sửa thành công");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
        $id = $request->id;
        $slider = Slider::find($id);
        $file_name = $slider->path;
        if ($file_name != '') {
            unlink('admin/assets/images/sliders/' . $file_name);
        }
        $slider->delete();
        // dd($slider);
        return redirect("/quantri/slider")->with("alert", "Đã xóa thành công");
    }
}
