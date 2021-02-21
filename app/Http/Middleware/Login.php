<?php


namespace App\Http\Middleware;


use Illuminate\Support\Facades\Cache;

class Login
{
    public function handle($request, \Closure $next)
    {
        if (!Cache::has('user')) {
            return redirect()->to(route('login'));
        }

        return $next($request);
    }
}
