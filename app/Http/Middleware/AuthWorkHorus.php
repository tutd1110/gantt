<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Mylib\Users;

class AuthWorkHorus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $users = new Users();
        $checkLogin = $users->checklogin();

        if($checkLogin) {
            return $next($request);
        }

        return redirect('/');
        //return redirect()->route('');

    }
}
