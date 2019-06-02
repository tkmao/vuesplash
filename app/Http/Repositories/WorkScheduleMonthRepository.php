<?php

namespace App\Repositories;

use App\Repositories\Models\WorkScheduleMonth;
use Illuminate\Support\Facades\DB;

class WorkScheduleMonthRepository implements WorkScheduleMonthRepositoryInterface
{
    /** @var WorkScheduleMonth */
    protected $workScheduleMonth;

    /**
     * @param WorkScheduleMonth $workScheduleMonth
     */
    public function __construct(WorkScheduleMonth $workScheduleMonth)
    {
        $this->workScheduleMonth = $workScheduleMonth;
    }

    /**
     * 勤務表提出状況取得
     *
     * @param int $userId
     * @return bool
     */
    public function exists(array $requestArray): bool
    {
        try {
            $workScheduleMonth = $this->workScheduleMonth
                                        ->where('user_id', $requestArray['userId'])
                                        ->where('yearmonth', $requestArray['yearmonth'])
                                        ->where('is_subumited', true)
                                        ->exists();

            return $workScheduleMonth;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 勤務表提出状況データ保存
     *
     * @param array $requestArray
     * @return void
     */
    public function store(array $requestArray): void
    {
        try {
            $workdate = new \Carbon\Carbon($requestArray['workschedules'][0]['workdate']);

            $workScheduleMonth = WorkScheduleMonth::updateOrCreate(
                ['user_id'      => $requestArray['workschedules'][0]['user_id'],
                 'yearmonth'    => $workdate->format('Ym')],
                ['is_subumited' => $requestArray['submit']]
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
