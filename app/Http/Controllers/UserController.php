<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\Models\User;
use App\Services\User\UserServiceInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /** @var UserServiceInterface */
    protected $userServiceInterface;

    public function __construct(
        UserServiceInterface $userServiceInterface
    ) {
        $this->userServiceInterface = $userServiceInterface;

        // 認証が必要
        $this->middleware('auth')->except(['index', 'download', 'show']);
    }

    /**
     * ユーザ一覧
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $users = User::orderBy(User::CREATED_AT, 'desc')->get();

        return $user ?? abort(404);
    }

    /**
     * ユーザ登録
     * @param string $id
     * @return User
     */
    public function store(Request $request)
    {
        dd($request->all());
        //$result = $this->userServiceInterface->store($request);
    }

    /**
     * ユーザ情報取得
     * @param string $id
     * @return User
     */
    public function get(Request $request)
    {
        $requestArray = $request->all();
        $user = $this->userServiceInterface->find($requestArray['userId']);

        return $user ?? abort(404);
    }

    /**
     * 全ユーザ情報取得
     * @return User
     */
    public function getAll(Request $request)
    {
        $users = $this->userServiceInterface->all();

        return $users ?? abort(404);
    }
}
