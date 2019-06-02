<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\User\WorkScheduleMonthServiceInterface;
use Illuminate\Http\Request;

class WorkScheduleMonthController extends Controller
{
    /** @var WorkScheduleMonthServiceInterface */
    protected $workScheduleMonthServiceInterface;

    public function __construct(
        WorkScheduleMonthServiceInterface $workScheduleMonthServiceInterface
    ) {
        $this->workScheduleMonthServiceInterface = $workScheduleMonthServiceInterface;

        // 認証が必要
        $this->middleware('auth')->except(['index', 'download', 'show']);
    }

    /**
     * 勤務表提出状況取得
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function isSubmitted(Request $request)
    {
        $requestArray = $request->all();
        $exists = $this->workScheduleMonthServiceInterface->exists($requestArray);

        $exists = $exists ? 1 : 0;

        return $exists ?? abort(404);
    }
}
