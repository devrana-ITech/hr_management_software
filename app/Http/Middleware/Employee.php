<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Response;

class Employee {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (Auth::User()->user_type != 'employee') {
            if (!$request->ajax()) {
                return back()->with('error', _lang('Permission denied !'));
            } else {
                return new Response('<h5 class="text-center text-danger">' . _lang('Permission denied !') . '</h5>');
            }
        }

        return $next($request);
    }
}
