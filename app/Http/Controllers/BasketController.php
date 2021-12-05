<?php

namespace App\Http\Controllers;

use App\Models\Order;

//use App\Services\BasketService;
use http\Client\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use BasketService;

class BasketController extends Controller
{

    /**
     * @var Request
     */
    private Request $request;


    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $products = BasketService::getBasket()->products;

        return view('basket.index', compact('products'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function checkout(Request $request)
    {
        $profile = null;
        $profiles = null;

        if (auth()->check()) {
            $user = auth()->user();
            $profiles = $user->profiles;
            $prof_id = $request->input('profile_id');

            if ($prof_id) {
                $profile = $user->profiles()->whereIdAndUserId($prof_id, $user->id)->first();
            }
        }

        return view('basket.checkout', compact('profiles', 'profile'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function add(int $id)
    {
        $quantity = $this->request->input('quantity') ?? 1;
        BasketService::getBasket()->increase($id, $quantity);

        if (! $this->request->ajax()) {
            return back();
        }

        $positions = BasketService::getBasket()->products->count();
        return view('basket.part.basket', compact('positions'));
    }


    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function plus(int $id): RedirectResponse
    {
        BasketService::getBasket()->increase($id);
        return redirect()->route('basket.index');
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function minus(int $id): RedirectResponse
    {
        BasketService::getBasket()->decrease($id);
        return redirect()->route('basket.index');
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function remove($id): RedirectResponse
    {
        BasketService::getBasket()->remove($id);

        return redirect()->route('basket.index');
    }

    /**
     * @return RedirectResponse
     */
    public function clear(): RedirectResponse
    {
        BasketService::getBasket()->clear();

        return redirect()->route('basket.index');
    }

    /**
     * @return RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function saveOrder(): RedirectResponse
    {
        $this->validate($this->request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|max:255',
            'address' => 'required|max:255',
        ]);

        $basket = BasketService::getBasket();
        $user_id = auth()->check() ? auth()->user()->id : null;
        $orderData = $this->request->all() + ['amount' => $basket->getAmount(), 'user_id' => $user_id];

        $order = Order::create($orderData);

        foreach ($basket->products as $product) {
            $order->items()->create([
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->pivot->quantity,
                'cost' => $product->price * $product->pivot->quantity,
            ]);
        }

        $basket->delete();

        return redirect()
            ->route('basket.success')
            ->with('order_id', $order->id);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function success()
    {
        if ($this->request->session()->exists('order_id')) {
            $order_id = $this->request->session()->pull('order_id');
            $order = Order::findOrFail($order_id);
            return view('basket.success', compact('order'));
        } else {
            return redirect()->route('basket.index');
        }
    }

    public function profile(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }
        if (!auth()->check()) {
            return response()->json(['error' => 'Нужна авторизация!'], 404);
        }
        $user = auth()->user();
        $profile_id = (int)$request->input('profile_id');
        if ($profile_id) {
            $profile = $user->profiles()->whereIdAndUserId($profile_id, $user->id)->first();
            if ($profile) {
                return response()->json(['profile' => $profile]);
            }
        }
        return response()->json(['error' => 'Профиль не найден!'], 404);
    }

}
