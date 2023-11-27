<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Slider;
use Illuminate\Http\Request;

class WebController extends Controller
{
    //
    public function index()
    {
        // Lấy danh sách các sản phẩm thuộc danh mục "Điện thoại" (sử dụng product_category_id)
        $phoneCategoryId = 1; // Thay thế bằng product_category_id của "Điện thoại"
        $phoneProducts = Product::where('product_category_id', $phoneCategoryId)->get();

        // Lấy danh sách các sản phẩm thuộc danh mục "Laptop" (sử dụng product_category_id)
        $laptopCategoryId = 2; // Thay thế bằng product_category_id của "Laptop"
        $laptopProducts = Product::where('product_category_id', $laptopCategoryId)->get();
        $categories = Category::all();
        $sliders = Slider::all();
        return view('FrontEnd.index', ['phoneProducts' => $phoneProducts, 'laptopProducts' => $laptopProducts, 'categories' => $categories, 'sliders' => $sliders]);
    }
    public function faq()
    {
        $categories = Category::all();
        return view('FrontEnd/faq/faq', ['categories' => $categories]);
    }
    public function contact()
    {
        $categories = Category::all();
        return view('FrontEnd/contact/contact', ['categories' => $categories]);
    }
}
