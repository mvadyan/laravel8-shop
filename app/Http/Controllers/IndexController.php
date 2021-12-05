<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function __invoke(Request $request)
    {
        $new = Product::whereNew(true)->latest()->limit(3)->get();
        $hit = Product::whereHit(true)->latest()->limit(3)->get();
        $sale = Product::whereSale(true)->latest()->limit(3)->get();

        return view('index', compact('new', 'hit', 'sale'));
    }
}
