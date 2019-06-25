<?php

namespace App\Repositories;

use App\Repositories\Models\ProjectStatus;

class ProjectStatusRepository implements ProjectStatusRepositoryInterface
{
    /** @var ProjectStatus */
    protected $projectStatus;

    /**
     * @param ProjectStatus $projectStatus
     */
    public function __construct(ProjectStatus $projectStatus)
    {
        $this->projectStatus = $projectStatus;
    }

    /**
     * 全PJ区分取得処理
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $categories = $this->projectStatus->orderBy('id', 'asc')->get();

            return $categories;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * PJ区分データ登録
     *
     * @param array $requestArray
     * @return void
     */
    public function store(array $requestArray): void
    {
        try {
            $projectStatus = new ProjectStatus;
            $projectStatus->name = $requestArray['name'];
            $projectStatus->is_deleted = false;
            $projectStatus->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * PJ区分データ編集
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $where = [ 'id' => $requestArray['id'] ];
            $update_values  = [ 'name' => $requestArray['name'],
                                'is_deleted' => false,
                            ];

            $this->projectStatus->where($where)->update($update_values);
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * PJ区分データ削除
     *
     * @param int $projectStatusId
     * @return void
     */
    public function delete(int $projectStatusId): void
    {
        try {
            $where = [ 'id' => $projectStatusId ];
            $update_values  = [ 'is_deleted' => true ];

            $this->projectStatus->where($where)->update($update_values);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
