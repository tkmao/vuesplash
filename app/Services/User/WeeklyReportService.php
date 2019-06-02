<?php

namespace App\Services\User;

use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\WeeklyReportRepositoryInterface;
use App\Repositories\WorkScheduleRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Services\User\WorkScheduleServiceInterface;
use Illuminate\Support\Facades\Storage;

class WeeklyReportService implements WeeklyReportServiceInterface
{
    /**
     * @var ProjectRepositoryInterface
     * @var WeeklyReportRepositoryInterface
     * @var WorkScheduleRepositoryInterface
     * @var UserRepositoryInterface
     * @var WorkScheduleServiceInterface
     */
    protected $projectRepositoryInterface;
    protected $weeklyReportRepositoryInterface;
    protected $workScheduleRepositoryInterface;
    protected $userRepositoryInterface;
    protected $workScheduleServiceInterface;

    /**
     * @param App\Repositories\ProjectRepositoryInterface  $projectRepositoryInterface  The project repository
     * @param App\Repositories\WeeklyReportRepositoryInterface  $weeklyReportRepositoryInterface  The weeklyReport repository
     * @param App\Repositories\WorkScheduleRepositoryInterface  $workScheduleRepositoryInterface  The workSchedule repository
     * @param App\Repositories\UserRepositoryInterface  $userRepositoryInterface  The user repository
     * @param App\Services\WorkScheduleServiceInterface  $workScheduleServiceInterface  The workSchedule service interface
     */
    public function __construct(
        ProjectRepositoryInterface $projectRepositoryInterface,
        WeeklyReportRepositoryInterface $weeklyReportRepositoryInterface,
        WorkScheduleRepositoryInterface $workScheduleRepositoryInterface,
        UserRepositoryInterface $userRepositoryInterface,
        WorkScheduleServiceInterface $workScheduleServiceInterface
    ) {
        $this->projectRepositoryInterface = $projectRepositoryInterface;
        $this->weeklyReportRepositoryInterface = $weeklyReportRepositoryInterface;
        $this->workScheduleRepositoryInterface = $workScheduleRepositoryInterface;
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->workScheduleServiceInterface = $workScheduleServiceInterface;
    }

    /**
     * 勤務表情報取得
     *
     * @param int $userId
     * @param int $weekNumber
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWorkSchedule(int $userId, int $weekNumber): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return $this->workScheduleRepositoryInterface->getWorkScheduleByUserIdWeekNumber($userId, $weekNumber);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 週報情報取得
     *
     * @param int $userId
     * @param int $weekNumber
     * @return \App\Repositories\Models\WeeklyReport
     */
    public function getWeeklyReport(int $userId, int $weekNumber): \App\Repositories\Models\WeeklyReport
    {
        try {
            return $this->weeklyReportRepositoryInterface->getWeeklyReport($userId, $weekNumber);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザ情報取得
     *
     * @param int $userId
     * @return \App\Repositories\Models\User
     */
    public function getUser(int $userId): \App\Repositories\Models\User
    {
        try {
            return $this->userRepositoryInterface->find($userId);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 週報データをJSONに変換
     *
     * @param array $allUserWorkSchedulesJSON
     * @param \Illuminate\Database\Eloquent\Collection $allUserweeklyReports
     * @return array $weeklyReportJSON
     */
    public function getWeeklyReportJSONAllUser(array $allUserWorkSchedulesJSON, \Illuminate\Database\Eloquent\Collection $allUserweeklyReports): array
    {
        try {
            $allUserweeklyReportJSON = $allUserWorkSchedulesJSON;

            foreach ($allUserweeklyReports as $userskey => $usersValue) {
                $weeklyReportJSON = null; // 週報データの配列

                if ($usersValue['weeklyReport']->isNotEmpty()) {
                    $weeklyReportJSON = $this->getWeeklyReportJSON($allUserWorkSchedulesJSON[$usersValue['id']]['workSchedule'], $usersValue['weeklyReport']);
                }
                $allUserweeklyReportJSON[$usersValue['id']]['weeklyReport'] = $weeklyReportJSON;
            }

            return $allUserweeklyReportJSON;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 週報データをJSONに変換
     *
     * @param array $workSchedulesJSON
     * @param \Illuminate\Database\Eloquent\Collection $weeklyReports
     * @return array $weeklyReportJSON
     */
    public function getWeeklyReportJSON(array $workSchedulesJSON, \Illuminate\Database\Eloquent\Collection $weeklyReports): array
    {
        try {
            $weeklyReportJSON = $workSchedulesJSON;

            if ($weeklyReports->isNotEmpty()) {
                foreach ($weeklyReports as $key => $weeklyReport) {
                    $weeklyReportJSON['weekly_report_id'] = $weeklyReport['id'];
                    $weeklyReportJSON['user_id'] = $weeklyReport['user_id'];
                    $weeklyReportJSON['week_number'] = $weeklyReport['week_number'];
                    $weeklyReportJSON['project_id'] = $weeklyReport['project_id'];
                    if (isset($weeklyReport['project'])) {
                        $weeklyReportJSON['project_code'] = $weeklyReport['project']['code'];
                        $weeklyReportJSON['project_name'] = $weeklyReport['project']['name'];
                        $weeklyReportJSON['project_conpmay_id'] = $weeklyReport['project']['conpmay_id'];
                    }
                    $weeklyReportJSON['nextweek_schedule'] = $weeklyReport['nextweek_schedule'];
                    $weeklyReportJSON['thismonth_dayoff'] = $weeklyReport['thismonth_dayoff'];
                    $weeklyReportJSON['site_information'] = $weeklyReport['site_information'];
                    $weeklyReportJSON['opinion'] = $weeklyReport['opinion'];
                    $weeklyReportJSON['is_subumited'] = $weeklyReport['is_subumited'];
                }
            }

            return $weeklyReportJSON;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 週報登録処理
     *
     * @param array $requestArray
     * @return void
     */
    public function store(array $requestArray): void
    {
        try {
            if ($requestArray['submit_type'] == 'save' && $requestArray['is_subumited'] !== 'true') {
                $requestArray['is_subumited'] = 0;
            } else {
                $requestArray['is_subumited'] = 1;
            }

            if (is_null($requestArray['weekly_report_id'])) {
                $this->weeklyReportRepositoryInterface->store($requestArray);
            } else {
                $this->weeklyReportRepositoryInterface->edit($requestArray);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 対象週のリスト作成
     *
     * @param int $weekNumber
     * @return array $targetWeeks
     */
    public function createTargetWeeks(int $weekNumber): array
    {
        try {
            $targetWeeks = [];

            $now = \Carbon\Carbon::now();
            $now->subMonth(3);
            $weekNumberIdx = $now->year . str_pad($now->weekOfYear, 2, 0, STR_PAD_LEFT);

            for ($dt = $now; $weekNumberIdx < $weekNumber; $dt->addWeek(1)) {
                $year = $dt->year;
                $week = str_pad($dt->weekOfYear, 2, 0, STR_PAD_LEFT);

                $weekNumberIdx = $year . $week;
                $weekStart = $year . 'W' . $week;
                $weekEnd = $year . 'W' . $week . '7';

                $targetWeeks[$weekNumberIdx] = date('Y/m/d', strtotime($weekStart)) . '(月) ～ ' . date('Y/m/d', strtotime($weekEnd)) . '(日)';
            }

            return $targetWeeks;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
