<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
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
        $totalUsers = Order::count();

        // Tính toán vị trí bắt đầu và kết thúc của kết quả trên trang hiện tại
        $startResult = ($currentPage - 1) * $perPage + 1;
        $endResult = min($startResult + $perPage - 1, $totalUsers);

        // Lấy danh sách người dùng với phân trang
        $orders = Order::orderBy("id", "asc");

        // Kiểm tra xem có dữ liệu tìm kiếm được gửi lên không
        if ($request->has('search')) {
            $search = $request->input('search');
            $orders->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }

        $orders = $orders->paginate($perPage);
        return view('Admin.order.order', [
            "orders" => $orders, "startResult" => $startResult,
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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //
        $id = $request->id;
        $order = Order::with('orderDetails')->find($id);
        return view('Admin.order.order-show', ['order' => $order]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        //
        $id = $request->id;
        $order = Order::with('orderDetails')->find($id);
        return view('Admin.order.order-edit', ['order' => $order]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //
        $id = $request->id;
        $order = Order::with('orderDetails')->find($id);
        $order->status = $request->status;
        $order->save();

        return redirect("/quantri/order");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
