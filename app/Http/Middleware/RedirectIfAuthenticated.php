<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            // return redirect('/home');
            // ログイン状態で非ログイン状態でのみアクセスできる機能にリクエストを送信した場合に /home へのリダイレクトが返却されています。
            // SPA 的にはページへのリダイレクト（HTML）が返るのは相応しくないので、先ほど作成したログインユーザー返却 API にリダイレクトするように修正しましょう。
            return redirect()->route('user'); // ★ 変更
        }

        return $next($request);
    }
}
