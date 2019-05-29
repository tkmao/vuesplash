<?php

namespace App\Services\User;

use App\Repositories\ProjectRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class ProjectService implements ProjectServiceInterface
{
    /**
     * @var ProjectRepositoryInterface
     */
    protected $projectRepositoryInterface;

    /**
     * @param App\Repositories\ProjectRepositoryInterface  $projectRepositoryInterface  The project repository
     */
    public function __construct(
        ProjectRepositoryInterface $projectRepositoryInterface
    ) {
        $this->projectRepositoryInterface = $projectRepositoryInterface;
    }

    /**
     * プロジェクトデータ取得
     *
     * @param int $projectId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getById(int $projectId): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return $this->projectRepositoryInterface->getById($projectId);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 全プロジェクトデータ取得
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $withOtherTable = true;
            return $this->projectRepositoryInterface->all($withOtherTable);
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
            $this->projectRepositoryInterface->store($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * プロジェクトデータ修正
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $this->projectRepositoryInterface->edit($requestArray);
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
            $this->projectRepositoryInterface->delete($projectId);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
