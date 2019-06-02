<?php

namespace App\Services\User;

use App\Repositories\WorkScheduleMonthRepositoryInterface;

class WorkScheduleMonthService implements WorkScheduleMonthServiceInterface
{
    /** @var WorkScheduleMonthRepositoryInterface */
    protected $workScheduleMonthRepositoryInterface;

    /**
     * @param  App\Repositories\WorkScheduleMonthRepositoryInterface  $workScheduleMonthRepositoryInterface  The work_schedule_month repository
     */
    public function __construct(
        WorkScheduleMonthRepositoryInterface $workScheduleMonthRepositoryInterface
    ) {
        $this->workScheduleMonthRepositoryInterface = $workScheduleMonthRepositoryInterface;
    }

    /**
     * 勤務表提出状況を取得する
     *
     * @param array $requestArray
     * @return bool
     */
    public function exists(array $requestArray): bool
    {
        try {
            return $this->workScheduleMonthRepositoryInterface->exists($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
