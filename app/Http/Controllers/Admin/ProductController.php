<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCatalogRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\ImageSaver;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    /**
     * @var ImageSaver
     */
    private ImageSaver $imageSaver;

    /**
     * @param ImageSaver $imageSaver
     */
    public function __construct(ImageSaver $imageSaver)
    {
        $this->imageSaver = $imageSaver;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $products = Product::paginate(5);
        $roots = Category::where('parent_id', 0)->get();

        return view('admin.product.index', compact('products', 'roots'));
    }

    /**
     * @param Category $category
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function category (Category $category)
    {
        $products = $category->products()->paginate(5);

        return view('admin.product.category', compact('category', 'products'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $items = Category::all();
        $brands = Brand::all();

        return view('admin.product.create', compact('items', 'brands'));
    }

    /**
     * @param ProductCatalogRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductCatalogRequest $request)
    {
        $request->merge([
            'new' => $request->has('new'),
            'hit' => $request->has('hit'),
            'sale' => $request->has('sale'),
        ]);
        $data = $request->all();
        $data['image'] = $this->imageSaver->upload($request, null, 'product');
        $product = Product::create($data);

        return redirect()
            ->route('admin.product.show', ['product' => $product->id])
            ->with('success', 'Новый товар успешно создан');
    }

    /**
     * @param Product $product
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(Product $product)
    {
        return view('admin.product.show', compact('product'));
    }

    /**
     * @param Product $product
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Product $product)
    {
        $items = Category::all();
        $brands = Brand::all();

        return view('admin.product.edit', compact('product', 'items', 'brands'));
    }

    /**
     * @param ProductCatalogRequest $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProductCatalogRequest $request, Product $product)
    {
        $request->merge([
            'new' => $request->has('new'),
            'hit' => $request->has('hit'),
            'sale' => $request->has('sale'),
        ]);
        $data = $request->all();
        $data['image'] = $this->imageSaver->upload($request, $product, 'product');

        $product->update($data);

        return redirect()
            ->route('admin.product.show', ['product' => $product->id])
            ->with('success', 'Товар был успешно обновлен');
    }

    /**
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        $this->imageSaver->remove($product, 'product');
        $product->delete();

        return redirect()
            ->route('admin.product.index')
            ->with('success', 'Товар каталога успешно удален');
    }
}
