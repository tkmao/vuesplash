<?php

namespace App\Services\User;

use App\Repositories\ProjectWorkRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\WorkScheduleMonthRepositoryInterface;
use App\Repositories\WorkScheduleRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class WorkScheduleService implements WorkScheduleServiceInterface
{
    /** @var ProjectWorkRepositoryInterface */
    protected $projectWorkRepositoryInterface;
    /** @var UserRepositoryInterface */
    protected $userRepositoryInterface;
    /** @var WorkScheduleMonthRepositoryInterface */
    protected $workScheduleMonthRepositoryInterface;
    /** @var WorkScheduleRepositoryInterface */
    protected $workScheduleRepositoryInterface;

    /**
     * @param  App\Repositories\ProjectWorkRepositoryInterface  $projectWorkRepositoryInterface  The projectWork repository
     * @param  App\Repositories\UserRepositoryInterface  $userRepositoryInterface  The user repository
     * @param  App\Repositories\WorkScheduleMonthRepositoryInterface  $workScheduleMonthRepositoryInterface  The workScheduleMonth repository
     * @param  App\Repositories\WorkScheduleRepositoryInterface  $workScheduleRepositoryInterface  The workSchedule repository
     */
    public function __construct(
        ProjectWorkRepositoryInterface $projectWorkRepositoryInterface,
        UserRepositoryInterface $userRepositoryInterface,
        WorkScheduleMonthRepositoryInterface $workScheduleMonthRepositoryInterface,
        WorkScheduleRepositoryInterface $workScheduleRepositoryInterface
    ) {
        $this->projectWorkRepositoryInterface = $projectWorkRepositoryInterface;
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->workScheduleMonthRepositoryInterface = $workScheduleMonthRepositoryInterface;
        $this->workScheduleRepositoryInterface = $workScheduleRepositoryInterface;
    }

    /**
     * 勤務表を取得する
     *
     * @param int $userId
     * @param \Carbon\Carbon $dateFrom
     * @param \Carbon\Carbon $dateTo
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByDate(int $userId, \Carbon\Carbon $dateFrom, \Carbon\Carbon $dateTo): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return $this->workScheduleRepositoryInterface->getByDate($userId, $dateFrom, $dateTo);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 勤務表を取得する（週報用）
     *
     * @param int $userId
     * @param string $weekNumber
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByWeekNumber(int $userId, string $weekNumber): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return $this->workScheduleRepositoryInterface->getByWeekNumber($userId, $weekNumber);
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * 全ユーザの勤務表を取得する
     *
     * @param \Carbon\Carbon $dateFrom
     * @param \Carbon\Carbon $dateTo
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUser(\Carbon\Carbon $dateFrom, \Carbon\Carbon $dateTo): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return $this->userRepositoryInterface->getWorkSchedule($dateFrom, $dateTo);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 最古の勤務表データ取得（週報登録画面用）
     *
     * @param int $userId
     * @return string
     */
    public function getOldestWorkdateByUserId(int $userId): string
    {
        try {
            return $this->workScheduleRepositoryInterface->getOldestWorkdateByUserId($userId);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 勤務表データ登録・更新
     *
     * @param array $requestArray
     * @return void
     */
    public function store(array $requestArray): void
    {
        try {
            // 新規での勤務表データ登録の場合、IDが存在しないので、その情報を登録と同時に取得
            $requestArray['workschedules'] = $this->workScheduleRepositoryInterface->store($requestArray['workschedules']);
            // プロジェクト勤務時間の取得
            $this->projectWorkRepositoryInterface->store($requestArray);
            // 勤務表提出
            $this->workScheduleMonthRepositoryInterface->store($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
