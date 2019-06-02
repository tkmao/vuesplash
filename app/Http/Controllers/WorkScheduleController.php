<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\User\WorkScheduleServiceInterface;
use Illuminate\Http\Request;

class WorkScheduleController extends Controller
{
    /** @var WorkScheduleServiceInterface */
    protected $workScheduleServiceInterface;

    public function __construct(
        WorkScheduleServiceInterface $workScheduleServiceInterface
    ) {
        $this->workScheduleServiceInterface = $workScheduleServiceInterface;

        // 認証が必要
        $this->middleware('auth')->except(['index', 'download', 'show']);
    }

    /**
     * 勤務表取得（勤務表登録画面用）
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $requestArray = $request->all();
        $userId = $requestArray['userId'];
        $yearmonth = $requestArray['yearmonth'];

        $date = (isset($yearmonth)) ? new \Carbon\Carbon($yearmonth . '01') : \Carbon\Carbon::now();

        $dateFrom = $date->copy()->startOfMonth();
        $dateTo = $date->copy()->endOfMonth();

        $workSchedule = $this->workScheduleServiceInterface->getByDate($userId, $dateFrom, $dateTo);

        return $workSchedule ?? abort(404);
    }

    /**
     * 勤務表取得（週報登録画面用）
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getweek(Request $request)
    {
        $requestArray = $request->all();
        $userId = $requestArray['userId'];
        $weekNumber = $requestArray['weekNumber'];
        $weekNumber = '201922';

        //$workSchedule = $this->workScheduleServiceInterface->getWorkSchedule($userId, $dateFrom, $dateTo);

        return $workSchedule ?? abort(404);
    }

    /**
     * 勤務表登録
     * @param string $id
     * @return User
     */
    public function store(Request $request)
    {
        $requestArray = $request->all();
        $result = $this->workScheduleServiceInterface->store($requestArray);
    }
}
