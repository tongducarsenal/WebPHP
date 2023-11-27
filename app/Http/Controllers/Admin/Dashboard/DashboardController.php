<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function index(Request $request)
    {
        $orders = Order::all();
        $products = Product::all();
        $users = User::all();
        // $orders = Order::all();
        // dd(count($products));

        $month = now()->month;
        $year = now()->year;

        $bestSellingProducts = DB::table('order_details')
            ->whereMonth('order_details.created_at', $month)
            ->whereYear('order_details.created_at', $year)
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->whereNotIn('orders.status', ['canceled']) // Chỉ lấy những đơn hàng có trạng thái khác "canceled"
            ->join('product_images', function ($join) {
                $join->on('products.id', '=', 'product_images.product_id')
                    ->where('product_images.id', '=', DB::raw("(select min(`id`) from `product_images` where `product_id` = `products`.`id`)")); // Lấy hình ảnh đầu tiên của mỗi sản phẩm
            })

            ->select('products.id', 'products.name', 'products.discount', DB::raw('SUM(order_details.qty) as total_sold'), 'product_images.path')
            ->groupBy('products.id', 'products.name', 'products.discount', 'product_images.path')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
        $countCancelledOrders = DB::table('orders')->where('status', 0)->count();
        $cancelledProducts = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->where('orders.status', '=', 0)
            ->join('product_images', function ($join) {
                $join->on('products.id', '=', 'product_images.product_id')
                    ->where('product_images.id', '=', DB::raw("(select min(`id`) from `product_images` where `product_id` = `products`.`id`)")); // Lấy hình ảnh đầu tiên của mỗi sản phẩm
            })
            ->select('products.id', 'products.name', DB::raw('SUM(order_details.qty) as total_cancelled'), 'product_images.path')
            ->groupBy('products.id', 'products.name', 'product_images.path')
            ->orderByDesc('total_cancelled')
            ->limit(5)
            ->get();
        return view(
            'Admin.dashboard.index',
            ['orders' => $orders, 'products' => $products, 'users' => $users, 'bestSellingProducts' => $bestSellingProducts, 'countCancelledOrders' => $countCancelledOrders, 'cancelledProducts' => $cancelledProducts]
        );

        // dd('hihihihihihihih');
    }

    // public function filter_by_date(Request $request)
    // {
    //     $data = $request->all();
    //     $from_date = Carbon::parse($data['from_date'])->startOfDay();
    //     $to_date = Carbon::parse($data['to_date'])->endOfDay();
    //     $get = DB::table('orders')
    //         ->join('orders_details', 'orders.id', '=', 'orders_details.order_id')
    //         ->select(DB::raw('date(orders.created_at) as created_at'), DB::raw('count(distinct orders.id) as order_count'), DB::raw('sum(distinct orders.total_order) as total_sale'), DB::raw('sum(orders_details.qty) as total_quantity'))
    //         ->whereBetween('orders.created_at', [$from_date, $to_date])
    //         ->groupBy('created_at')
    //         ->orderBy('created_at', 'ASC')
    //         ->get();
    //     $chart_data = [];
    //     foreach ($get as $key => $val) {
    //         $chart_data[] = array(
    //             'period' => $val->created_at,
    //             'order' => $val->order_count,
    //             'sale' => $val->total_sale,
    //             'quantity' => $val->total_quantity
    //         );
    //     }
    //     echo $data = json_encode($chart_data);
    // }
    // public function dashboard_filter(Request $request)
    // {


    //     // $today = Carbon::now('Asia/Ho_Chi_Minh')->format('d-m-Y H:i:s');
    //     // $tomorrow = Carbon::now('Asia/Ho_Chi_Minh')->addDay()->format('d-m-Y H:i:s');
    //     // $lastWeek = Carbon::now('Asia/Ho_Chi_Minh')->subWeek()->format('d-m-Y H:i:s');
    //     // $sub15days = Carbon::now('Asia/Ho_Chi_Minh')->subdays(15)->format('d-m-Y H:i:s');
    //     // $sub30days = Carbon::now('Asia/Ho_Chi_Minh')->subdays(30)->format('d-m-Y H:i:s');
    //     $data = $request->all();
    //     $dauthangnay = Carbon::now('Asia/Ho_Chi_Minh')->startOfMonth()->toDateString();
    //     $dau_thangtruoc = Carbon::now('Asia/Ho_Chi_Minh')->subMonth()->startOfMonth()->toDateString();
    //     $cuoi_thangtruoc = Carbon::now('Asia/Ho_Chi_Minh')->subMonth()->endOfMonth()->toDateString();

    //     $sub7days = Carbon::now('Asia/Ho_Chi_Minh')->subdays(7)->toDateString();
    //     $sub365days = Carbon::now('Asia/Ho_Chi_Minh')->subdays(365)->toDateString();
    //     $now = Carbon::now('Asia/Ho_Chi_Minh')->endOfMonth()->toDateString();

    //     if ($data['dashboard_value'] == '7ngay') {
    //         $get = DB::table('orders')
    //             ->join('orders_details', 'orders.id', '=', 'orders_details.order_id')
    //             ->select(DB::raw('date(orders.created_at) as created_at'), DB::raw('count(distinct orders.id) as order_count'), DB::raw('sum(distinct orders.total_order) as total_sale'), DB::raw('sum(orders_details.qty) as total_quantity'))
    //             ->whereBetween('orders.created_at', [$sub7days, $now])
    //             ->groupBy('created_at')
    //             ->orderBy('created_at', 'ASC')
    //             ->get();
    //     } elseif ($data['dashboard_value'] == 'thangtruoc') {
    //         $get = DB::table('orders')
    //             ->join('orders_details', 'orders.id', '=', 'orders_details.order_id')
    //             ->select(DB::raw('date(orders.created_at) as created_at'), DB::raw('count(distinct orders.id) as order_count'), DB::raw('sum(distinct orders.total_order) as total_sale'), DB::raw('sum(orders_details.qty) as total_quantity'))
    //             ->whereBetween('orders.created_at', [$dau_thangtruoc, $cuoi_thangtruoc])
    //             ->groupBy('created_at')
    //             ->orderBy('created_at', 'ASC')
    //             ->get();
    //     } elseif ($data['dashboard_value'] == 'thangnay') {
    //         $get = DB::table('orders')
    //             ->join('orders_details', 'orders.id', '=', 'orders_details.order_id')
    //             ->select(DB::raw('date(orders.created_at) as created_at'), DB::raw('count(distinct orders.id) as order_count'), DB::raw('sum(distinct orders.total_order) as total_sale'), DB::raw('sum(orders_details.qty) as total_quantity'))
    //             ->whereBetween('orders.created_at', [$dauthangnay, $now])
    //             ->groupBy('created_at')
    //             ->orderBy('created_at', 'ASC')
    //             ->get();
    //     } else {
    //         $get = DB::table('orders')
    //             ->join('orders_details', 'orders.id', '=', 'orders_details.order_id')
    //             ->select(DB::raw('date(orders.created_at) as created_at'), DB::raw('count(distinct orders.id) as order_count'), DB::raw('sum(distinct orders.total_order) as total_sale'), DB::raw('sum(orders_details.qty) as total_quantity'))
    //             ->whereBetween('orders.created_at', [$sub365days, $now])
    //             ->groupBy('created_at')
    //             ->orderBy('created_at', 'ASC')
    //             ->get();
    //     }
    //     $chart_data = [];
    //     foreach ($get as $key => $val) {
    //         $chart_data[] = array(
    //             'period' => $val->created_at,
    //             'order' => $val->order_count,
    //             'sale' => $val->total_sale,
    //             'quantity' => $val->total_quantity
    //         );
    //     }

    //     echo $data = json_encode($chart_data);
    // }
    // public function day_order()
    // {

    //     $sub60days = Carbon::now('Asia/Ho_Chi_Minh')->subdays(60)->toDateString();
    //     $now = Carbon::now('Asia/Ho_Chi_Minh')->endOfMonth()->toDateString();
    //     $get = DB::table('orders')
    //         ->join('orders_details', 'orders.id', '=', 'orders_details.order_id')
    //         ->select(DB::raw('date(orders.created_at) as created_at'), DB::raw('count(distinct orders.id) as order_count'), DB::raw('sum(distinct orders.total_order) as total_sale'), DB::raw('sum(orders_details.qty) as total_quantity'))
    //         ->whereBetween('orders.created_at', [$sub60days, $now])
    //         ->groupBy('created_at')
    //         ->orderBy('created_at', 'ASC')
    //         ->get();
    //     $chart_data = [];
    //     foreach ($get as $key => $val) {
    //         $chart_data[] = array(
    //             'period' => $val->created_at,
    //             'order' => $val->order_count,
    //             'sale' => $val->total_sale,
    //             'quantity' => $val->total_quantity
    //         );
    //     }
    //     echo $data = json_encode($chart_data);
    // }
}
