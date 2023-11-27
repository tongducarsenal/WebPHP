<?php

namespace App\Http\Controllers\Web\Blog;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    //
    public function index()
    {
        $categories = Category::all();
        return view('FrontEnd.blog.blog', ['categories' => $categories]);
    }
    public function show()
    {
        return view('FrontEnd.blog.blog-details');
    }
}
