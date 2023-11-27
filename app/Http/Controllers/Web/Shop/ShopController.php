<?php

namespace App\Http\Controllers\Web\Shop;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Http\Request;


class ShopController extends Controller
{
    //
    public function index()
    {
        $perPage = 9; // Số lượng mục trên mỗi trang
        $currentPage = request()->query('page', 1); // Lấy trang hiện tại từ query string, mặc định là trang 1

        // Lấy tổng số lượng người dùng trong cơ sở dữ liệu
        $totalUsers = Product::count();

        // Tính toán vị trí bắt đầu và kết thúc của kết quả trên trang hiện tại
        $startResult = ($currentPage - 1) * $perPage + 1;
        $endResult = min($startResult + $perPage - 1, $totalUsers);

        // Lấy danh sách người dùng với phân trang
        $products = Product::orderBy("id", "asc");

        // Kiểm tra xem có dữ liệu tìm kiếm được gửi lên không
        // if ($request->has('search')) {
        //     $search = $request->input('search');
        //     $products->where(function ($query) use ($search) {
        //         $query->where('name', 'like', '%' . $search . '%');
        //     });
        // }

        $products = $products->paginate($perPage);
        $categories = Category::all();
        $brands = Brand::all();


        // dd($categories);
        // Truyền các giá trị vào view
        return view('FrontEnd.shop.shop', [
            "products" => $products,
            "startResult" => $startResult,
            "endResult" => $endResult,
            "totalResults" => $totalUsers,
            "categories" => $categories,
            "brands" => $brands
        ]);
    }
    public function show(Request $request)
    {
        $id = $request->id;
        $product = Product::with('category')->find($id);

        // Truy vấn các sản phẩm liên quan, ví dụ: lấy sản phẩm cùng loại (category_id) và không bao gồm sản phẩm hiện tại ($id)
        $relatedProducts = Product::where('product_category_id', $product->product_category_id)
            ->where('id', '!=', $id)
            ->limit(4) // Giới hạn số lượng sản phẩm liên quan
            ->get();
        $categories = Category::all();
        // $category = Category::find($id)->toArray();
        // dd($product->qty);

        // dd($relatedProducts);
        return view('FrontEnd.shop.product', ['product' => $product, 'relatedProducts' => $relatedProducts, 'categories' => $categories]);
    }
    public function category($categoryName)
    {
        $perPage = 9; // Số lượng mục trên mỗi trang
        $currentPage = request()->query('page', 1); // Lấy trang hiện tại từ query string, mặc định là trang 1

        // Lấy danh mục theo tên
        $category = Category::where('name', $categoryName)->first();

        if (!$category) {
            // Xử lý trường hợp nếu danh mục không tồn tại
            abort(404);
        }

        // Lấy tổng số lượng sản phẩm trong danh mục
        $totalProducts = Product::where('product_category_id', $category->id)->count();

        // Tính toán vị trí bắt đầu và kết thúc của kết quả trên trang hiện tại
        $startResult = ($currentPage - 1) * $perPage + 1;
        $endResult = min($startResult + $perPage - 1, $totalProducts);

        // Lấy danh sách sản phẩm thuộc danh mục đã chọn với phân trang
        $products = Product::where('product_category_id', $category->id)
            ->orderBy("id", "asc")
            ->paginate($perPage);
        $categories = Category::all();
        $brands = Brand::all();


        return view('FrontEnd.shop.shop', [
            'products' => $products,
            'categories' => $category,
            'startResult' => $startResult,
            'endResult' => $endResult,
            'totalResults' => $totalProducts,
            "categories" => $categories,
            'brands' => $brands,
        ]);
    }

    public function brand(Request $request)
    {
        $perPage = 9; // Số lượng mục trên mỗi trang
        $currentPage = request()->query('page', 1); // Lấy trang hiện tại từ query string, mặc định là trang 1

        // Lấy tổng số lượng sản phẩm trong danh mục
        // $totalProducts = Product::where('brand_id', $request->id)->count();



        // Lấy danh sách tất cả thương hiệu
        $brands = Brand::all();

        // Lấy danh sách sản phẩm ban đầu (trước khi lọc)
        $products = Product::orderBy("id", "asc");

        // Kiểm tra nếu có yêu cầu lọc thương hiệu
        if ($request->has('brands')) {
            $selectedBrands = $request->input('brands');

            // Lọc sản phẩm theo thương hiệu đã chọn
            $products = $products->whereIn('brand_id', $selectedBrands);
            // $totalProducts = Product::where('brand_id', $request->id)->count();
            // Tính toán vị trí bắt đầu và kết thúc của kết quả trên trang hiện tại
            $totalProducts = $products->count();
            $startResult = ($currentPage - 1) * $perPage + 1;
            $endResult = min($startResult + $perPage - 1, $totalProducts);
        } else {
            return redirect('shop');
        }

        // Tiếp tục thực hiện phân trang và các xử lý khác
        $products = $products->paginate($perPage);
        $categories = Category::all();

        // $brandName = Brand::find($selectedBrands)->toArray();

        // dd($brandName);

        // Truyền các giá trị vào view
        return view('FrontEnd.shop.shop', [
            "products" => $products,
            "startResult" => $startResult,
            "endResult" => $endResult,
            "totalResults" => $totalProducts,
            "categories" => $categories,
            "brands" => $brands,
            // "brandName" => $brandName,
            "selectedBrands" => $selectedBrands ?? [], // Để xác định những thương hiệu đã chọn
        ]);
    }

    public function filterPrice(Request $request)
    {
        $perPage = 9; // Số lượng mục trên mỗi trang
        // Lấy trang hiện tại từ query string, mặc định là trang 1
        $currentPage = $request->query('page', 1);

        // Lấy danh sách sản phẩm ban đầu (trước khi lọc)
        // $products = Product::orderBy("id", "asc");

        // Lấy giá trị min và max từ request
        $minPrice = floatval(str_replace('$', '', $request->input('price_min')));
        $maxPrice = floatval(str_replace('$', '', $request->input('price_max')));

        // Thực hiện truy vấn lọc sản phẩm theo giá
        $products = Product::whereBetween('price', [$minPrice, $maxPrice]);

        // Lấy tổng số lượng người dùng trong cơ sở dữ liệu
        $totalResults = $products->count();

        // Tính toán vị trí bắt đầu và kết thúc của kết quả trên trang hiện tại
        $startResult = ($currentPage - 1) * $perPage + 1;
        $endResult = min($startResult + $perPage - 1, $totalResults);

        // Lưu lại các tham số phân trang trong yêu cầu lọc
        $products = $products->paginate($perPage)->withQueryString();

        // Các xử lý khác ở đây
        $categories = Category::all();
        $brands = Brand::all();


        return view('FrontEnd.shop.shop', [
            'products' => $products,
            'startResult' => $startResult,
            'endResult' => $endResult,
            'totalResults' => $totalResults,
            "categories" => $categories,
            'brands' => $brands,
        ]);
    }

    public function filterRam(Request $request)
    {
        $perPage = 9; // Số lượng mục trên mỗi trang
        $currentPage = $request->query('page', 1); // Lấy trang hiện tại từ query string, mặc định là trang 1

        // Lấy giá trị RAM đã chọn từ request
        $selectedRam = $request->input('ram');

        // Thực hiện truy vấn lọc sản phẩm theo RAM đã chọn
        $products = Product::whereHas('productDetail', function ($query) use ($selectedRam) {
            $query->where('size', $selectedRam);
        });

        // Lấy tổng số lượng sản phẩm trong cơ sở dữ liệu
        $totalResults = $products->count();

        // Tính toán vị trí bắt đầu và kết thúc của kết quả trên trang hiện tại
        $startResult = ($currentPage - 1) * $perPage + 1;
        $endResult = min($startResult + $perPage - 1, $totalResults);

        // Lưu lại các tham số phân trang trong yêu cầu lọc
        $products = $products->paginate($perPage)->withQueryString();

        // Các xử lý khác ở đây
        $categories = Category::all();
        $brands = Brand::all();

        return view('FrontEnd.shop.shop', [
            'products' => $products,
            'startResult' => $startResult,
            'endResult' => $endResult,
            'totalResults' => $totalResults,
            "categories" => $categories,
            'brands' => $brands,
        ]);
    }
    public function filterColor(Request $request)
    {
        $perPage = 9; // Số lượng mục trên mỗi trang
        $currentPage = $request->query('page', 1); // Lấy trang hiện tại từ query string, mặc định là trang 1

        // Lấy giá trị RAM đã chọn từ request
        $selectedRam = $request->input('color');

        // Thực hiện truy vấn lọc sản phẩm theo RAM đã chọn
        $products = Product::whereHas('productDetail', function ($query) use ($selectedRam) {
            $query->where('color', $selectedRam);
        });

        // Lấy tổng số lượng sản phẩm trong cơ sở dữ liệu
        $totalResults = $products->count();

        // Tính toán vị trí bắt đầu và kết thúc của kết quả trên trang hiện tại
        $startResult = ($currentPage - 1) * $perPage + 1;
        $endResult = min($startResult + $perPage - 1, $totalResults);

        // Lưu lại các tham số phân trang trong yêu cầu lọc
        $products = $products->paginate($perPage)->withQueryString();

        // Các xử lý khác ở đây
        $categories = Category::all();
        $brands = Brand::all();

        return view('FrontEnd.shop.shop', [
            'products' => $products,
            'startResult' => $startResult,
            'endResult' => $endResult,
            'totalResults' => $totalResults,
            "categories" => $categories,
            'brands' => $brands,
        ]);
    }
}
