<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\User\WeeklyReportServiceInterface;
use App\Services\User\WorkScheduleServiceInterface;
use Illuminate\Http\Request;

class WeeklyReportController extends Controller
{
    /** @var WeeklyReportServiceInterface */
    protected $weeklyReportServiceInterface;
    /** @var WorkScheduleServiceInterface */
    protected $workScheduleServiceInterface;

    /**
     * @param App\Services\WeeklyReportServiceInterface  $weeklyReportServiceInterface  The weeklyReport service interface
     * @param App\Services\WorkScheduleServiceInterface  $workScheduleServiceInterface  The workSchedule service interface
     */
    public function __construct(
        WeeklyReportServiceInterface $weeklyReportServiceInterface,
        WorkScheduleServiceInterface $workScheduleServiceInterface
    ) {
        $this->weeklyReportServiceInterface = $weeklyReportServiceInterface;
        $this->workScheduleServiceInterface = $workScheduleServiceInterface;
    }

    /**
     * 週報情報取得
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $requestArray = $request->all();
        $userId = $requestArray['userId'];
        $weekNumber = $requestArray['weekNumber'];

        // 週報情報取得
        $weeklyReport = $this->weeklyReportServiceInterface->get($userId, $weekNumber);

        return $weeklyReport ?? abort(404);
    }

    /**
     * 週報登録処理
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $requestArray = $request->all();
        $this->weeklyReportServiceInterface->store($requestArray['weeklyreport']);
    }

    /**
     * 全ユーザ週報情報取得
     *
     * @param Request $request
     * @return void
     */
    public function getAllUser(Request $request)
    {
        $requestArray = $request->all();
        $weekNumber = $requestArray['weekNumber'];
        $weekNumber = '201919';

        // 週報情報取得
        $weeklyReports = $this->weeklyReportServiceInterface->getAllUser($weekNumber);

        return $weeklyReports ?? abort(404);
    }
}
