<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

    // use AuthenticatesUsers;

    // /**
    //  * Where to redirect users after login.
    //  *
    //  * @var string
    //  */
    // protected $redirectTo = '/home';

    private $userEloquent;
    private $hasher;
    private $loginedUser;

    /**
     * @param User $user
     */
    public function __construct(
        User $user,
        Hasher $hasher
    ) {
        $this->middleware('guest')->except('logout');
        $this->userEloquent = $user;
        $this->hasher = $hasher;
    }

    // protected function authenticated(Request $request, $user)
    // {
    //     return $user;
    // }

    // protected function loggedOut(Request $request)
    // {
    //     // セッションを再生成する
    //     $request->session()->regenerate();

    //     return response()->json();
    // }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $this->attemptSignin($credentials)) {
            throw new AuthenticationException('Unauthenticated.');
        }

        $token = hash('sha256', Str::random(60));
        $this->loginedUser->forceFill([
            'api_token' => $token,
        ])->save();

        $this->loginedUser->api_token = $token;

        return response()->json($this->loginedUser);
    }

    private function attemptSignin(array $credentials): bool
    {
        $user = $this->userEloquent::where('email', $credentials['email'])->first();

        if ($user && $this->hasher->check($credentials['password'], $user->password)) {
            $this->loginedUser = $user;

            return true;
        }

        return false;
    }
}
