<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\User\UserContractServiceInterface;
use Illuminate\Http\Request;

class UserContractController extends Controller
{
    /** @var UserContractServiceInterface */
    protected $userContractServiceInterface;

    public function __construct(
        UserContractServiceInterface $userContractServiceInterface
    ) {
        $this->userContractServiceInterface = $userContractServiceInterface;
        // 認証が必要
        $this->middleware('auth')->except(['index', 'download', 'show']);
    }

    /**
     * ユーザ契約登録
     * @return UserContract
     */
    public function store(Request $request)
    {
        $requestArray = $request->all();
        $result = $this->userContractServiceInterface->store($requestArray['userContract']);
    }

    /**
     * ユーザ契約編集
     * @return UserContract
     */
    public function edit(Request $request)
    {
        $requestArray = $request->all();
        $result = $this->userContractServiceInterface->edit($requestArray['userContract']);
    }

    /**
     * ユーザ契約削除
     * @return UserContract
     */
    public function delete(Request $request)
    {
        $requestArray = $request->all();
        $result = $this->userContractServiceInterface->delete($requestArray['userContractId']);
    }
}
