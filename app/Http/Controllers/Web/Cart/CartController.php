<?php

namespace App\Http\Controllers\Web\Cart;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    //
    public function index()
    {
        $categories = Category::all();
        $carts = Cart::content();
        $total = Cart::total();
        $subtotal = Cart::subtotal();

        // dd($carts, $total, $subtotal);
        return view('FrontEnd.shop.shopping-cart', ['categories' => $categories, 'total' => $total, 'subtotal' => $subtotal, 'carts' => $carts]);
    }


    public function create(Request $request)
    {
        if ($request->ajax()) {
            $productId = $request->productId; // Use the correct parameter name

            $product = Product::find($productId);

            $response['cart'] = Cart::add([
                'id' => $product->id,
                'name' => $product->name,
                'qty' => 1,
                'price' => $product->discount ?? $product->price,
                'weight' => $product->weight ?? 0,
                'options' => [
                    'images' => $product->productImage,
                ]
            ]);

            $response['count'] = Cart::count();
            $response['total'] = Cart::total();

            return $response;
        }
    }

    public function addCartInDetails(Request $request)
    {
        if ($request->ajax()) {
            $productId = $request->productId; // Use the correct parameter name

            $product = Product::find($productId);

            $response['cart'] = Cart::add([
                'id' => $product->id,
                'name' => $product->name,
                'qty' => 1,
                'price' => $product->discount ?? $product->price,
                'weight' => $product->weight ?? 0,
                'options' => [
                    'images' => $product->productImage,
                ]
            ]);

            $response['count'] = Cart::count();
            $response['total'] = Cart::total();

            return $response;
        }
    }

    public function delete(Request $request)
    {
        if ($request->ajax()) {
            $response['cart'] = Cart::remove($request->rowId);

            $response['count'] = Cart::count();
            $response['total'] = Cart::total();
            $response['subtotal'] = Cart::subtotal();

            return $response;
        }

        return back();
    }



    public function update(Request $request)
    {
        if ($request->ajax()) {
            $response['cart'] = Cart::update($request->rowId, $request->qty);

            $response['count'] = Cart::count();
            $response['total'] = Cart::total();
            $response['subtotal'] = Cart::subtotal();

            return $response;
        }
    }
    public function destroy()
    {
        Cart::destroy();
    }
}
