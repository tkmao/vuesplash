<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\User\ProjectStatusServiceInterface;
use Illuminate\Http\Request;

class ProjectStatusController extends Controller
{
    /** @var ProjectStatusServiceInterface */
    protected $projectStatusServiceInterface;

    public function __construct(
        ProjectStatusServiceInterface $projectStatusServiceInterface
    ) {
        $this->projectStatusServiceInterface = $projectStatusServiceInterface;
        // 認証が必要
        $this->middleware('auth');
    }

    /**
     * PJステータス一覧
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(Request $request)
    {
        $onlyActive = false;
        $projectStatuses = $this->projectStatusServiceInterface->all($onlyActive);

        return $projectStatuses ?? abort(404);
    }

    /**
     * PJステータス一覧（アクティブデータのみ）
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOnlyActive(Request $request)
    {
        $onlyActive = true;
        $projectStatuses = $this->projectStatusServiceInterface->all($onlyActive);

        return $projectStatuses ?? abort(404);
    }

    /**
     * PJステータス登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $requestArray = $request->all();
        $this->projectStatusServiceInterface->store($requestArray['projectStatus']);
    }

    /**
     * PJステータス修正
     *
     * @param Request $request
     * @return void
     */
    public function edit(Request $request)
    {
        $requestArray = $request->all();
        $this->projectStatusServiceInterface->edit($requestArray['projectStatus']);
    }

    /**
     * PJステータス削除
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request)
    {
        $requestArray = $request->all();
        $this->projectStatusServiceInterface->delete($requestArray['projectStatusId']);
    }
}
