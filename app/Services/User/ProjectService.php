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
     * 全プロジェクトデータ取得
     *
     * @param bool $onlyActive
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(bool $onlyActive): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return $this->projectRepositoryInterface->all($onlyActive);
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
