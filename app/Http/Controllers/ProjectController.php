<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\User\ProjectServiceInterface;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /** @var ProjectServiceInterface */
    protected $projectServiceInterface;

    public function __construct(
        ProjectServiceInterface $projectServiceInterface
    ) {
        $this->projectServiceInterface = $projectServiceInterface;
        // 認証が必要
        $this->middleware('auth');
    }

    /**
     * プロジェクト一覧
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(Request $request)
    {
        $onlyActive = false;
        $projects = $this->projectServiceInterface->all($onlyActive);

        return $projects ?? abort(404);
    }

    /**
     * プロジェクト一覧（アクティブデータのみ）
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOnlyActive(Request $request)
    {
        $onlyActive = true;
        $projects = $this->projectServiceInterface->all($onlyActive);

        return $projects ?? abort(404);
    }

    /**
     * プロジェクト登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $requestArray = $request->all();
        $this->projectServiceInterface->store($requestArray['project']);
    }

    /**
     * プロジェクト修正
     *
     * @param Request $request
     * @return void
     */
    public function edit(Request $request)
    {
        $requestArray = $request->all();
        $this->projectServiceInterface->edit($requestArray['project']);
    }

    /**
     * プロジェクト削除
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request)
    {
        $requestArray = $request->all();
        $this->projectServiceInterface->delete($requestArray['projectId']);
    }
}
