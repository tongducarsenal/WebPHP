<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\AddProductRequest;
use App\Http\Requests\Product\EditProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
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
        $totalUsers = Product::count();

        // Tính toán vị trí bắt đầu và kết thúc của kết quả trên trang hiện tại
        $startResult = ($currentPage - 1) * $perPage + 1;
        $endResult = min($startResult + $perPage - 1, $totalUsers);

        // Lấy danh sách người dùng với phân trang
        $products = Product::orderBy("id", "asc");

        // Kiểm tra xem có dữ liệu tìm kiếm được gửi lên không
        if ($request->has('search')) {
            $search = $request->input('search');
            $products->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }

        $products = $products->paginate($perPage);

        // Truyền các giá trị vào view
        return view('Admin.product.product', [
            "products" => $products,
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
        $brands = Brand::all()->toArray();
        $categories = Category::all()->toArray();
        // dd($brands);
        return view('Admin.product.product-create', ['brands' => $brands], ['categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddProductRequest $request)
    {
        //
        $product = new Product();

        $product->brand_id = $request->brand_id;
        $product->product_category_id = $request->product_category_id;
        $product->name = $request->name;
        $product->content = $request->content;
        $product->price = $request->price;
        $product->discount = $request->discount;
        $product->weight = $request->weight;
        $product->sku = $request->sku;
        $product->qty = $request->qty;
        $product->tag = $request->tag;
        $product->featured = $request->featured;
        $product->state = $request->state;
        $product->description = $request->description;
        $product->save();

        return redirect("/quantri/product")->with("alert", "Đã thêm thành công");
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //
        $id = $request->id;
        $products = Product::find($id);
        return view('Admin.product.product-show', ["products" => $products]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        //
        $id = $request->id;
        $product = Product::find($id);
        $brands = Brand::all();
        $categories = Category::all();
        // dd($categories[0]['name']);
        // dd($brand['id']);
        // dd($product['brand_id']);
        return view('Admin.product.product-edit', ['product' => $product, 'brands' => $brands, 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditProductRequest $request)
    {
        //
        $id = $request->id;
        $product = Product::find($id);

        $product->brand_id = $request->brand_id;
        $product->product_category_id = $request->product_category_id;
        $product->name = $request->name;
        $product->content = $request->content;
        $product->price = $request->price;
        $product->discount = $request->discount;
        $product->weight = $request->weight;
        $product->sku = $request->sku;
        $product->qty = $request->qty;
        $product->tag = $request->tag;
        $product->featured = $request->featured;
        $product->state = $request->state;
        $product->description = $request->description;
        $product->save();

        return redirect("/quantri/product")->with("alert", "Đã sửa thành công");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        // detele
        $id = $request->id;
        $product = Product::find($id);
        $product->delete();
        return redirect("/quantri/product")->with('alert', 'Đã xóa thành công');
    }
}
