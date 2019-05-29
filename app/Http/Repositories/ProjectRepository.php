<?php

namespace App\Repositories;

use App\Repositories\Models\Project;

class ProjectRepository implements ProjectRepositoryInterface
{
    /** @var Project */
    protected $project;

    /**
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * 全プロジェクトデータ取得
     *
     * @param bool $withOtherTable
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(bool $withOtherTable): \Illuminate\Database\Eloquent\Collection
    {
        try {
            if ($withOtherTable) {
                $project = $this->project->with(['category', 'company', 'user', 'projectStatus'])->where('is_deleted', false)->get();
            } else {
                $project = $this->project->where('is_deleted', false)->get();
            }

            return $project;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * プロジェクトデータ取得
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getById(int $id): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $project = $this->project->with(['category', 'company', 'user', 'projectStatus'])->where('id', '=', $id)->get();

            return $project;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * プロジェクトデータ登録
     *
     * @param array $requestArray
     * @return void
     */
    public function store(array $requestArray): void
    {
        try {
            $project = new Project;
            $project->code = $requestArray['projectCode'];
            $project->name = $requestArray['projectName'];
            $project->category_id = $requestArray['categoryId'];
            $project->company_id = $requestArray['companyId'];
            $project->user_id = $requestArray['userId'];
            $project->status_id = $requestArray['projectStatusId'];
            $project->is_deleted = false;
            $project->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * プロジェクトデータ編集
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $where = [ 'id' => $requestArray['projectId'] ];
            $update_values  = [ 'code' => $requestArray['projectCode'],
                                'name' => $requestArray['projectName'],
                                'category_id' => $requestArray['categoryId'],
                                'company_id' => $requestArray['companyId'],
                                'user_id' => $requestArray['userId'],
                                'status_id' => $requestArray['projectStatusId'],
                                'is_deleted' => false,
                            ];

            $this->project->where($where)->update($update_values);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * プロジェクトデータ削除
     *
     * @param int $projectId
     * @return void
     */
    public function delete(int $projectId): void
    {
        try {
            $where = [ 'id' => $projectId ];
            $update_values  = [ 'is_deleted' => true ];

            $this->project->where($where)->update($update_values);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
