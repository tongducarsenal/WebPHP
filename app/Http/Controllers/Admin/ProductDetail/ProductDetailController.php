<?php

namespace App\Http\Controllers\Admin\ProductDetail;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductDetail\AddProductDetailRequest;
use App\Http\Requests\ProductDetail\EditProductDetailRequest;
use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $productId = $request->id;

        $productDetails = ProductDetail::where('product_id', $productId);

        if ($request->has('search')) {
            $search = $request->input('search');
            $productDetails->where('color', 'like', '%' . $search . '%');
        }

        $productDetails = $productDetails->get();

        return view('Admin.product.detail.product-detail', ['productDetails' => $productDetails, 'productId' => $productId]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $product_id = $request->id;
        // Lấy tên sản phẩm từ bảng Product
        $productName = Product::find($product_id)->name;

        // Truyền tên sản phẩm vào view
        return view('Admin.product.detail.product-detail-create', ['productName' => $productName, 'product_id' => $product_id]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(AddProductDetailRequest $request)
    {
        //
        $id = $request->id;
        // $product_id = Product::with('product')->find($id);
        $productDetail = new ProductDetail();
        $productDetail->product_id = $id;
        $productDetail->color = $request->color;
        $productDetail->size = $request->size;
        $productDetail->qty = $request->qty;
        $productDetail->save();

        // dd($id);
        return redirect("/quantri/product/product-detail/{$id}")->with('alert', 'Thêm thành công chi tiết sản phẩm');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductDetail $productDetail)
    {
        //
        return view('Admin.product.detail.product-detail');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        //
        $id = $request->id;
        $productDetail = ProductDetail::with('product')->find($id)->toArray();
        return view('Admin.product.detail.product-detail-edit', ['productDetail' => $productDetail]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditProductDetailRequest $request)
    {
        //
        $id = $request->id;
        $productDetail = ProductDetail::find($id);

        $productDetail->color = $request->color;
        $productDetail->size = $request->size;
        $productDetail->qty = $request->qty;
        $productDetail->save();

        // dd($product_id);
        return redirect("/quantri/product/product-detail/{$productDetail->product_id}")->with("alert", "Đã sửa thành công");
        // dd($productDetail);
        // return view('Admin.product.detail.product-detail-edit');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
        $id = $request->id;
        $productDetail = ProductDetail::find($id);
        $productDetail->delete();

        return redirect("/quantri/product/product-detail/{$productDetail->product_id}")->with('alert', 'Đã xóa thành công');
    }
}
