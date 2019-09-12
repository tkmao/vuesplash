<?php

namespace App\Services\User;

use App\Repositories\ProjectStatusRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class ProjectStatusService implements ProjectStatusServiceInterface
{
    /**
     * @var ProjectStatusRepositoryInterface
     */
    protected $projectStatusRepositoryInterface;

    /**
     * @param App\Repositories\ProjectStatusRepositoryInterface  $projectStatusRepositoryInterface  The projectStatus repository
     */
    public function __construct(
        ProjectStatusRepositoryInterface $projectStatusRepositoryInterface
    ) {
        $this->projectStatusRepositoryInterface = $projectStatusRepositoryInterface;
    }

    /**
     * 全ユーザタイプデータ取得
     *
     * @param bool $onlyActive
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(bool $onlyActive): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return $this->projectStatusRepositoryInterface->all($onlyActive);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザタイプデータ登録
     *
     * @param array $requestArray
     * @return void
     */
    public function store(array $requestArray): void
    {
        try {
            $this->projectStatusRepositoryInterface->store($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザタイプデータ修正
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $this->projectStatusRepositoryInterface->edit($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザタイプデータ削除
     *
     * @param int $projectStatusId
     * @return void
     */
    public function delete(int $projectStatusId): void
    {
        try {
            $this->projectStatusRepositoryInterface->delete($projectStatusId);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
