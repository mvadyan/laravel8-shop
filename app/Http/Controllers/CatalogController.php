<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductFilter;
use http\Client\Response;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $roots = Category::where('parent_id', 0)->get();
        $brands = Brand::popular();
        return view('catalog.index', compact('roots', 'brands'));
    }

    /**
     * @param Category $category
     * @param ProductFilter $filters
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function category(Category $category, ProductFilter $filters)
    {
        $products = Product::categoryProducts($category->id)
            ->filterProducts($filters)
            ->paginate(6)
            ->withQueryString();

        return view('catalog.category', compact('category', 'products'));
    }

    /**
     * @param Brand $brand
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function brand(Brand $brand, ProductFilter $filters)
    {
        $products = $brand
            ->products() // возвращает построитель запроса
            ->filterProducts($filters)
            ->paginate(6)
            ->withQueryString();
        return view('catalog.brand', compact('brand', 'products'));
    }

    /**
     * @param Product $product
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function product(Product $product)
    {
        return view('catalog.product', compact('product'));
    }
}
