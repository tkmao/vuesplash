<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\User\CompanyServiceInterface;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /** @var CompanyServiceInterface */
    protected $companyServiceInterface;

    public function __construct(
        CompanyServiceInterface $companyServiceInterface
    ) {
        $this->companyServiceInterface = $companyServiceInterface;
        // 認証が必要
        $this->middleware('auth');
    }

    /**
     * 全企業取得
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(Request $request)
    {
        $onlyActive = true;
        $companies = $this->companyServiceInterface->all($onlyActive);

        return $companies ?? abort(404);
    }

    /**
     * 企業登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $requestArray = $request->all();
        $this->companyServiceInterface->store($requestArray['company']);
    }

    /**
     * 企業修正
     *
     * @param Request $request
     * @return void
     */
    public function edit(Request $request)
    {
        $requestArray = $request->all();
        $this->companyServiceInterface->edit($requestArray['company']);
    }

    /**
     * 企業削除
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request)
    {
        $requestArray = $request->all();
        $this->companyServiceInterface->delete($requestArray['companyId']);
    }
}
