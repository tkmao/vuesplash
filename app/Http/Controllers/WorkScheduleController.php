<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\Models\WorkSchedule;
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
     * 勤務表一覧
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $requestPara = $request->all();

        $yearmonth = $requestPara['yearmonth'];
        $user_id = 1;
        if ($yearmonth != null) {
            $yearmonth = $yearmonth . '01';
            $dt = new \Carbon\Carbon($yearmonth);
        } else {
            $dt = \Carbon\Carbon::now();
        }

        $dateFrom = $dt->copy()->startOfMonth();
        $dateTo = $dt->copy()->endOfMonth();

        $workSchedule = WorkSchedule::with(['projectWork'])->where('user_id', $user_id)->whereBetween('workdate', [$dateFrom, $dateTo])->orderBy('workdate')->get();

        return $workSchedule ?? abort(404);
    }

    /**
     * 勤務表登録
     * @param string $id
     * @return User
     */
    public function store(Request $request)
    {
        dd($request->all());
        //$result = $this->workScheduleServiceInterface->store($request);
        dd('storeUser', $id, $request->all(), gettype($request));

        return $user ?? abort(404);
    }
}
