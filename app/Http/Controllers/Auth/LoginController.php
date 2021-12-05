<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    /**
     * @param Request $request
     * @param $user
     * @return RedirectResponse
     */
    protected function authenticated(Request $request, $user): RedirectResponse
    {
        $route = 'user.index';
        $message = 'Вы успешно вошли в личный кабинет';

        if ($user->admin) {
            $route = 'admin.index';
            $message = 'Вы успешно вошли в панель управления';
        }

        return redirect()->route($route)
            ->with('success', $message);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    protected function loggedOut(Request $request): RedirectResponse
    {
        return redirect()->route('user.login')
            ->with('success', 'Вы успешно вышли из личного кабинета');
    }
}
