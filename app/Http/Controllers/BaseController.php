<?php


namespace App\Http\Controllers;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class BaseController extends Controller
{
    protected $userId;
    protected $username;

    public function __construct()
    {
        $user = Cache::get('user');
        $this->userId = $user['id'];
        $this->username = $user['name'];
        View::share('username', $this->username);
    }
}
