<?php

namespace App\Http\Controllers\Web\CheckOut;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckOut\CheckOutRequest;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Utilities\Constant;
use App\Utilities\VNPay;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CheckOutController extends Controller
{
    //
    public function index()
    {
        $carts = Cart::content();
        $total = Cart::total();
        $subtotal = Cart::subtotal();
        $categories = Category::all();

        return view('FrontEnd.checkout.check-out', ['categories' => $categories, 'total' => $total, 'subtotal' => $subtotal, 'carts' => $carts]);
    }

    public function create(CheckOutRequest $request)
    {
        // 01. Thêm đơn hàng
        $order = new Order();
        $order->user_id = $request->user_id;
        $order->first_name = $request->first_name;
        $order->last_name = $request->last_name;
        $order->company_name = $request->company_name;
        $order->country = $request->country;
        $order->street_address = $request->street_address;
        $order->postcode_zip = $request->postcode_zip;
        $order->town_city = $request->town_city;
        $order->email = $request->email;
        $order->phone = $request->phone;
        $order->payment_type = $request->payment_type;
        $order->status = Constant::order_status_ReceiveOrders;
        $order->save();


        // dd($order);

        // 02. thêm chi tiết đơn hàng
        $carts = Cart::content();

        foreach ($carts as $cart) {
            $data = [
                'order_id' => $order->id,
                'product_id' => $cart->id,
                'qty' => $cart->qty,
                'amount' => $cart->price,
                'total' => $cart->price * $cart->qty,
            ];

            // dd($order);
            $orderDetail = new OrderDetail();
            $orderDetail->order_id = $data['order_id'];
            $orderDetail->product_id = $data['product_id'];
            $orderDetail->qty = $data['qty'];
            $orderDetail->amount = $data['amount'];
            $orderDetail->total = $data['total'];
            $orderDetail->save();
            // dd($request->all());

            // 3. Trừ số lượng sản phẩm trong cơ sở dữ liệu
            $product = Product::find($cart->id);
            $product->qty -= $cart->qty;
            $product->save();
        }

        if ($request->payment_type == 'pay_later') {
            // 03. gửi email
            $total = Cart::total();
            $subtotal = Cart::subtotal();
            $this->sendEmail($order, $total, $subtotal);
            // 04. xóa giỏ hàng
            Cart::destroy();
            // 05. trả về thông báo

            return redirect('/checkout/result')->with('alert', 'Thanh toán thành công ! Vui lòng kiểm tra email');
        }

        if ($request->payment_type == 'online_payment') {
            //01. lấy url thanh toán VNPay

            $data_url = VNPay::vnpay_create_payment([
                'vnp_TxnRef' => $order->id, //ma id don hang
                'vnp_OrderInfo' => 'Mô tả đơn hàng ở đây',
                'vnp_Amount' => Cart::total(0, ',', '.') * 100000,

            ]);
            // dd($data_url);
            //chuyuển  hướng tới địa chỉ trên 
            return redirect()->to($data_url);
        }
    }

    public function vnPayCheck(Request $request)
    {
        //lấy data từ url do vnpay gửi qua $vnp_returnurl
        $vnp_ResponseCode = $request->get('vnp_ResponseCode'); //mã phản hồi thanh toán
        $vnp_TxnRef = $request->get('vnp_TxnRef'); //mã số order_id
        $vnp_Amount = $request->get('vnp_Amount'); //số tiền thanh toán

        //kiểm tra data, xem kết quả giao dịch trả về từ vn pay hợp lệ hay không  
        if ($vnp_ResponseCode != null) {
            //nếu thành công
            if ($vnp_ResponseCode == 00) {
                //gửi email
                $order = Order::with('orderDetails')->find($vnp_TxnRef);
                $total = Cart::total(0, '.', '');
                $subtotal = Cart::subtotal(0, '.', '');
                $this->sendEmail($order, $total, $subtotal);
                Cart::destroy();
                //xóa giỏ hàng
                // dd($order);
                return redirect('checkout/result')->with('alert', 'Thanh toán thành công ! Vui lòng kiểm tra thông tin Email.');
            } else {
                //xóa đơn hàng đã thêm vào 
                $orderDel = Order::find($vnp_TxnRef);
                $orderDel->delete();
                // thông báo lỗi 

                return redirect('checkout/result')->with('alert', 'Thanh toán thất bại ! Vui lòng kiểm tra lại');
            }
        }
    }

    private function sendEmail($order, $total, $subtotal)
    {
        $email_to = $order->email;

        Mail::send('FrontEnd.checkout.email', compact('order', 'total', 'subtotal'), function ($message) use ($email_to) {
            $message->from('duogbachdev@gmail.com', 'DphoneS Shop');
            $message->to($email_to, $email_to);
            $message->subject('Order notification');
        });
    }

    public function result()
    {
        $categories = Category::all();
        return view('FrontEnd.checkout.result', ['categories' => $categories]);
    }
}
