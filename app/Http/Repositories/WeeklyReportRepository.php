<?php

namespace App\Repositories;

use App\Repositories\Models\WeeklyReport;

class WeeklyReportRepository implements WeeklyReportRepositoryInterface
{
    /** @var WeeklyReport */
    protected $weeklyReport;

    /**
     * @param WeeklyReport $weeklyReport
     */
    public function __construct(WeeklyReport $weeklyReport)
    {
        $this->weeklyReport = $weeklyReport;
    }

    /**
     * 週報情報取得
     *
     * @param int $userId
     * @param int $weekNumber
     * @return \App\Repositories\Models\WeeklyReport $weeklyReport
     */
    public function get(int $userId, int $weekNumber): \App\Repositories\Models\WeeklyReport
    {
        try {
            $weeklyReport = $this->weeklyReport->with('project')->where('user_id', $userId)->where('week_number', $weekNumber)->first();
            if (!$weeklyReport) {
                $weeklyReport = new WeeklyReport();
            }

            return $weeklyReport;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 全ユーザ週報情報取得
     *
     * @param int $weekNumber
     * @return \Illuminate\Database\Eloquent\Collection $weeklyReport
     */
    public function getAllUser(int $weekNumber): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $weeklyReport = $this->weeklyReport->with('project')->where('week_number', $weekNumber)->orderBy('id', 'asc')->get();
            if (!$weeklyReport) {
                $weeklyReport = new WeeklyReport();
            }

            return $weeklyReport;
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
            $weeklyReport = new WeeklyReport;
            $weeklyReport->user_id = $requestArray['user_id'];
            $weeklyReport->week_number = $requestArray['week_number'];
            $weeklyReport->project_id = $requestArray['project_id'];
            $weeklyReport->nextweek_schedule = $requestArray['nextweek_schedule'];
            $weeklyReport->thismonth_dayoff = $requestArray['thismonth_dayoff'];
            $weeklyReport->site_information = $requestArray['site_information'];
            $weeklyReport->opinion = $requestArray['opinion'];
            $weeklyReport->is_subumited = $requestArray['is_subumited'];
            $weeklyReport->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 週報更新処理
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $weeklyReport = $this->weeklyReport->find($requestArray['id']);
            $weeklyReport->user_id = $requestArray['user_id'];
            $weeklyReport->week_number = $requestArray['week_number'];
            $weeklyReport->project_id = $requestArray['project_id'];
            $weeklyReport->nextweek_schedule = $requestArray['nextweek_schedule'];
            $weeklyReport->thismonth_dayoff = $requestArray['thismonth_dayoff'];
            $weeklyReport->site_information = $requestArray['site_information'];
            $weeklyReport->opinion = $requestArray['opinion'];
            $weeklyReport->is_subumited = $requestArray['is_subumited'];
            $weeklyReport->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
