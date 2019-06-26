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
     * @param bool $onlyActive
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(bool $onlyActive): \Illuminate\Database\Eloquent\Collection
    {
        try {
            if ($onlyActive) {
                $projects = $this->project->with(['category', 'company', 'user', 'projectStatus'])->where('is_deleted', false)->get();
            } else {
                $projects = $this->project->with(['category', 'company', 'user', 'projectStatus'])->get();
            }

            return $projects;
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
            $project->code = $requestArray['code'];
            $project->name = $requestArray['name'];
            $project->category_id = $requestArray['category_id'];
            $project->company_id = $requestArray['company_id'];
            $project->user_id = $requestArray['user_id'];
            $project->status_id = $requestArray['status_id'];
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
            $where = [ 'id' => $requestArray['id'] ];
            $update_values  = [ 'code' => $requestArray['code'],
                                'name' => $requestArray['name'],
                                'category_id' => $requestArray['category_id'],
                                'company_id' => $requestArray['company_id'],
                                'user_id' => $requestArray['user_id'],
                                'status_id' => $requestArray['status_id'],
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
