<?php

namespace App\Http\Controllers\Admin\ProductImage;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductImage\AddProductImageRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Slug\Slug;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $product_id = $request->id;
        // Tìm sản phẩm cùng với danh sách hình ảnh của nó
        $product = Product::with('productImage')->find($product_id);

        // Lấy danh sách hình ảnh của sản phẩm
        $productImages = $product->productImage->toArray();

        // Sử dụng dd để kiểm tra
        // dd($productImages);

        // Lấy đường dẫn của hình ảnh đầu tiên trong danh sách
        // $path = $productImages[0]["path"];
        // dd($path);

        // Sử dụng dd để kiểm tra
        // dd($productImages->toArray());

        // Lấy tên sản phẩm
        // $productName = $productImage->name;
        // dd($productImage);
        return view('Admin.product.image.product-image', ["product" => $product, "productImages" => $productImages, 'product_id' => $product_id]);
        // dd($productImages);
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
    public function store(AddProductImageRequest $request)
    {
        // Kiểm tra và lưu trữ hình ảnh
        // $product_id = $request->id;
        // dd($pei)
        // Kiểm tra và lưu trữ hình ảnh
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Tìm sản phẩm với ID tương ứng
            $product = Product::find($request->id);

            // Kiểm tra xem sản phẩm có tồn tại không
            if ($product) {
                // Lấy slug từ tên sản phẩm
                $productName = $product->name;
                $slug = Slug::getSlug($productName);

                // Đặt tên cho tệp hình ảnh sử dụng slug và timestamp
                $imageName = $slug . '_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move("admin/assets/images/products/", $imageName);

                // Lưu thông tin vào bảng ProductImage
                $productImage = new ProductImage();
                $productImage->path = $imageName; // Tên hình ảnh đã lưu
                $productImage->product_id = $request->id;
                $productImage->save();

                return redirect("/quantri/product/show/{$request->id}");
            } else {
                // Xử lý trường hợp không tìm thấy sản phẩm với ID đã cho
                dd('failed to save product');
            }
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(ProductImage $productImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductImage $productImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductImage $productImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        $productImage = ProductImage::with('product')->find($id);
        // $product_id= Product::find($id);
        // dd($productImage->product->id);

        // Kiểm tra xem bản ghi productImage có tồn tại không
        if ($productImage) {
            $file_name = $productImage->path;
            if ($file_name != '') {
                unlink('admin/assets/images/products/' . $file_name);
            }
            // Xóa bản ghi productImage
            $productImage->delete();
        }

        // Sau khi xóa, bạn có thể chuyển hướng đến trang khác
        return redirect("/quantri/product/product-image/{$productImage->product->id}");
    }
}
