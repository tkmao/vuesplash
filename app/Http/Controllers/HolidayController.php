<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\User\HolidayServiceInterface;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /** @var HolidayServiceInterface */
    protected $holidayServiceInterface;

    public function __construct(
        HolidayServiceInterface $holidayServiceInterface
    ) {
        $this->holidayServiceInterface = $holidayServiceInterface;
        // 認証が必要
        $this->middleware('auth');
    }

    /**
     * 休日一覧
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        dd('holiday index');
        $holidays = $this->holidayServiceInterface->getAll();

        return $holidays ?? abort(404);
    }

    /**
     * 休日登録
     * @param string $id
     * @return Holiday
     */
    public function store(Request $request)
    {
        dd($request->all());
        //$result = $this->holidayServiceInterface->store($request);
        dd('storeUser', $id, $request->all(), gettype($request));

        return $user ?? abort(404);
    }

    /**
     * 休日一覧
     * @param string $id
     * @return Holiday
     */
    public function getAll(Request $request)
    {
        $holidays = $this->holidayServiceInterface->getAllHoliday();

        return $holidays ?? abort(404);
    }
}
