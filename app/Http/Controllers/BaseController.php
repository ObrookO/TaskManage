<?php


namespace App\Http\Controllers;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class BaseController extends Controller
{
    protected $userId;
    protected $username;

    public function __construct()
    {
        $this->middleware(function ($request, \Closure $next) {
            if (!Session::has('user')) {
                return redirect()->to(route('login'));
            }

            $user = Session::get('user');
            $this->userId = $user['id'];
            $this->username = $user['name'];
            View::share('username', $this->username);

            return $next($request);
        });
    }
}
