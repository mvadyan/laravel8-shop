<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $pages = Page::all();
        return view('admin.page.index', compact('pages'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $parents = Page::where('parent_id', 0)->get();
        return view('admin.page.create', compact('parents'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:100',
            'parent_id' => 'required|regex:~^[0-9]+$~',
            'slug' => 'required|max:100|unique:pages|regex:~^[-_a-z0-9]+$~i',
            'content' => 'required',
        ]);

        $page = Page::create($request->all());

        return redirect()
            ->route('admin.page.show', ['page' => $page->id])
            ->with('success', 'Новая страница успешно создана');
    }

    /**
     * @param Page $page
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(Page $page)
    {
        return view('admin.page.show', compact('page'));
    }

    /**
     * @param Page $page
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Page $page)
    {
        $parents = Page::where('parent_id', 0)->get();

        return view('admin.page.edit', compact('page', 'parents'));
    }

    /**
     * @param Request $request
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Page $page)
    {
        $this->validate($request, [
            'name' => 'required|max:100',
            'parent_id' => 'required|regex:~^[0-9]+$~|not_in:' . $page->id,
            'slug' => 'required|max:100|unique:pages,slug,' . $page->id . ',id|regex:~^[-_a-z0-9]+$~i',
            'content' => 'required',
        ]);

        $page->update($request->all());

        return redirect()
            ->route('admin.page.index', ['page' => $page->id])
            ->with('success', 'Страница была успешно отредактирована');
    }

    /**
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Page $page)
    {
        if ($page->children->count()) {
            return back()->withErrors('Нельзя удалить страницу, у которой есть дочерние');
        }

        $this->removeImages($page->content);
        $page->delete();
        return redirect()
            ->route('admin.page.index')
            ->with('success', 'Страница сайта успешно удалена');
    }

    /**
     * @param $content
     */
    private function removeImages($content)
    {
        $dom = new \DomDocument();
        $dom->loadHtml($content);
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');
           // dd($src);
           //"http://127.0.0.1:8000/storage/page/uc9Fw4rFAYPVcA7MCk1tk8YfNc3beoGSWRyFJi4W.jpg"
            $pattern = '~(\w+)\.(jpeg|jpg|png)~';
            preg_match($pattern, $src, $match);
            //dd($match);

            if (preg_match($pattern, $src, $match)) {
                $name = $match[0];
                if (Storage::disk('public')->exists('page/' . $name)) {
                    Storage::disk('public')->delete('page/' . $name);
                }
            }
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function uploadImage(Request $request) {
        $this->validate($request, ['image' => [
            'mimes:jpeg,jpg,png',
            'max:5000'
        ]]);
        $path = $request->file('image')->store('page', 'public');
        $url = Storage::disk('public')->url($path);
        return response()->json(['image' => $url]);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function removeImage(Request $request)
    {
        $path = parse_url($request->image, PHP_URL_PATH);
        $path = str_replace('/storage/', '', $path);

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        return 'Не удалось удалить изображение';
    }
}
