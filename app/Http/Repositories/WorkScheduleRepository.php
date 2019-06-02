<?php

namespace App\Repositories;

use App\Repositories\Models\WorkSchedule;
use Illuminate\Support\Facades\DB;

class WorkScheduleRepository implements WorkScheduleRepositoryInterface
{
    /** @var WorkSchedule */
    protected $workSchedule;

    /**
     * @param WorkSchedule $workSchedule
     */
    public function __construct(WorkSchedule $workSchedule)
    {
        $this->workSchedule = $workSchedule;
    }

    /**
     * 勤務表取得
     *
     * @param int $userId
     * @param \Carbon\Carbon $dateFrom
     * @param \Carbon\Carbon $dateTo
     * @return \Illuminate\Database\Eloquent\Collection $workSchedule
     */
    public function getByDate(int $userId, \Carbon\Carbon $dateFrom, \Carbon\Carbon $dateTo): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $workSchedule = $this->workSchedule::with(['projectWork'])
                                                ->where('user_id', $userId)
                                                ->whereBetween('workdate', [$dateFrom, $dateTo])
                                                ->orderBy('workdate')->get();

            return $workSchedule;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 勤務表取得
     *
     * @param int $userId
     * @param int $weekNumber
     * @return \Illuminate\Database\Eloquent\Collection $workSchedule
     */
    public function getWorkScheduleByUserIdWeekNumber(int $userId, int $weekNumber): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $workSchedule = $this->workSchedule->with(['projectWork.project', 'holiday'])->where('user_id', $userId)->where('week_number', $weekNumber)->orderBy('workdate', 'asc')->get();
            if (!$workSchedule) {
                $workSchedule = new WorkSchedule();
            }

            return $workSchedule;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 最古の勤務日データ取得
     *
     * @return string $oldestWrokdate
     */
    public function getOldestWorkdate(): string
    {
        try {
            $oldestWrokdate = $this->workSchedule->min('workdate');

            return $oldestWrokdate;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 勤務表データ保存
     *
     * @param array $workSchedules
     * @return array $workSchedulesHasId
     */
    public function store(array $workSchedules): array
    {
        try {
            $workSchedulesHasId = DB::transaction(function () use ($workSchedules) {
                foreach ($workSchedules as $key => $value) {
                    // 勤務表データが存在する場合と存在しない場合で場合分け
                    if (is_null($value['id'])) {
                        $workSchedule = new WorkSchedule;
                        $workSchedule->user_id = \Auth::user()['id'];
                        $workSchedule->workdate = $value['workdate'];
                        $workSchedule->week_number = $value['week_number'];
                        $workSchedule->detail = $value['detail'];
                        $workSchedule->starttime_hh = $value['starttime_hh'];
                        $workSchedule->starttime_mm = $value['starttime_mm'];
                        $workSchedule->endtime_hh = $value['endtime_hh'];
                        $workSchedule->endtime_mm = $value['endtime_mm'];
                        $workSchedule->breaktime = $value['breaktime'];
                        $workSchedule->breaktime_midnight = $value['breaktime_midnight'];
                        $workSchedule->is_paid_holiday = (isset($value['is_paid_holiday']) ? $value['is_paid_holiday'] : false);
                        $workSchedule->save();

                        $workSchedules[$key]['id'] = $workSchedule->id;
                    } else {
                        $where = [ 'id' => $value['id'] ];
                        $update_values  = [ 'starttime_hh' => $value['starttime_hh'],
                                            'starttime_mm' => $value['starttime_mm'],
                                            'endtime_hh' => $value['endtime_hh'],
                                            'endtime_mm' => $value['endtime_mm'],
                                            'breaktime' => $value['breaktime'],
                                            'breaktime_midnight' => $value['breaktime_midnight'],
                                            'detail' => $value['detail'],
                                            'is_paid_holiday' => (isset($value['is_paid_holiday']) ? $value['is_paid_holiday'] : false),
                                        ];

                        $this->workSchedule->where($where)->update($update_values);
                    }
                }
                return $workSchedules;
            });

            return $workSchedulesHasId;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
