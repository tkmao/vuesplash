<?php

namespace App\Services\User;

use App\Repositories\WorkScheduleRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class WorkScheduleService implements WorkScheduleServiceInterface
{
    /**
     * @var WorkScheduleRepositoryInterface
     */
    protected $workScheduleRepositoryInterface;

    /**
     * @param App\Repositories\WorkScheduleRepositoryInterface  $workScheduleRepositoryInterface  The user repository
     */
    public function __construct(
        WorkScheduleRepositoryInterface $workScheduleRepositoryInterface
    ) {
        $this->workScheduleRepositoryInterface = $workScheduleRepositoryInterface;
    }

    /**
     * 勤務表データ取得
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return $this->workScheduleRepositoryInterface->getById($userId);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザデータ登録
     *
     * @param array $requestArray
     * @return void
     */
    public function store(array $requestArray): void
    {
        try {
            $this->workScheduleRepositoryInterface->store($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザデータ修正
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $this->workScheduleRepositoryInterface->edit($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザデータ削除
     *
     * @param int $userId
     * @return void
     */
    public function delete(int $userId): void
    {
        try {
            $this->workScheduleRepositoryInterface->delete($userId);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
