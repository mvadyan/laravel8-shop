<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandCatalogRequest;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Services\ImageSaver;

class BrandController extends Controller
{
    /**
     * @var ImageSaver
     */
    private ImageSaver $imageSaver;

    public function __construct(ImageSaver $imageSaver)
    {
        $this->imageSaver = $imageSaver;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $brands = Brand::all();

        return view('admin.brand.index', compact('brands'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('admin.brand.create');
    }

    /**
     * @param BrandCatalogRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(BrandCatalogRequest $request)
    {
        $data = $request->all();
        $data['image'] = $this->imageSaver->upload($request, null, 'brand');

        $brand = Brand::create($data);

        return redirect()
            ->route('admin.brand.show', ['brand' => $brand->id])
            ->with('success', 'Новый бренд успешно создан');
    }

    /**
     * @param Brand $brand
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(Brand $brand)
    {
        return view('admin.brand.show', compact('brand'));
    }

    /**
     * @param Brand $brand
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Brand $brand)
    {
        return view('admin.brand.edit', compact('brand'));
    }

    /**
     * @param Request $request
     * @param Brand $brand
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(BrandCatalogRequest $request, Brand $brand)
    {
        $data = $request->all();
        $data['image'] = $this->imageSaver->upload($request, $brand, 'brand');
        $brand->update($data);

        return redirect()
            ->route('admin.brand.show', ['brand' => $brand->id])
            ->with('success', 'Бренд был успешно отредактирован');
    }

    /**
     * @param Brand $brand
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Brand $brand)
    {
        if ($brand->products->count()) {
            return back()->withErrors('Нельзя удалить бренд, у которого есть товары');
        }

        $this->imageSaver->remove($brand, 'brand');
        $brand->delete();

        return redirect()
            ->route('admin.brand.index')
            ->with('success', 'Бренд каталога успешно удален');
    }
}
