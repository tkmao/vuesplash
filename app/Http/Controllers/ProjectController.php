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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        dd('project index');
        $projects = $this->projectServiceInterface->getAll();

        return $projects ?? abort(404);
    }

    /**
     * プロジェクト一覧
     * @param string $id
     * @return Project
     */
    public function getAll(Request $request)
    {
        $projects = $this->projectServiceInterface->all();

        return $projects ?? abort(404);
    }

    /**
     * プロジェクト登録
     * @param string $id
     * @return User
     */
    public function store(Request $request)
    {
        dd($request->all());
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
        dd($request->all());
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
        dd($request->all());
        $requestArray = $request->all();
        $this->projectServiceInterface->delete($requestArray['projectId']);
    }
}
