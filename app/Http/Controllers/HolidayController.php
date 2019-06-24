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
     * @param string $id
     * @return Holiday
     */
    public function getAll(Request $request)
    {
        $holidays = $this->holidayServiceInterface->getAllHoliday();

        return $holidays ?? abort(404);
    }

    /**
     * 休日登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $requestArray = $request->all();
        $this->holidayServiceInterface->store($requestArray['holiday']);
    }

    /**
     * 休日修正
     *
     * @param Request $request
     * @return void
     */
    public function edit(Request $request)
    {
        $requestArray = $request->all();
        $this->holidayServiceInterface->edit($requestArray['holiday']);
    }

    /**
     * 休日削除
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request)
    {
        $requestArray = $request->all();
        $this->holidayServiceInterface->delete($requestArray['holidayId']);
    }
}
