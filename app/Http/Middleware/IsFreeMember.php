<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsFreeMember
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->member_type === 'free') {
            return $next($request);
        }

        return redirect()->route('/')->with('error', 'このページは無料会員限定です。');
    }
}

?>