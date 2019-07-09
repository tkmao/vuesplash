<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\User\UserTypeServiceInterface;
use Illuminate\Http\Request;

class UserTypeController extends Controller
{
    /** @var UserTypeServiceInterface */
    protected $userTypeServiceInterface;

    public function __construct(
        UserTypeServiceInterface $userTypeServiceInterface
    ) {
        $this->userTypeServiceInterface = $userTypeServiceInterface;
        // 認証が必要
        $this->middleware('auth');
    }

    /**
     * ユーザタイプ一覧
     * @param string $id
     * @return UserType
     */
    public function getAll(Request $request)
    {
        $onlyActive = false;
        $userTypes = $this->userTypeServiceInterface->all($onlyActive);

        return $userTypes ?? abort(404);
    }

    /**
     * ユーザタイプ登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $requestArray = $request->all();
        $this->userTypeServiceInterface->store($requestArray['userType']);
    }

    /**
     * ユーザタイプ修正
     *
     * @param Request $request
     * @return void
     */
    public function edit(Request $request)
    {
        $requestArray = $request->all();
        $this->userTypeServiceInterface->edit($requestArray['userType']);
    }

    /**
     * ユーザタイプ削除
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request)
    {
        $requestArray = $request->all();
        $this->userTypeServiceInterface->delete($requestArray['userTypeId']);
    }
}
