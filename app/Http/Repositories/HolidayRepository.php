<?php

namespace App\Repositories;

use App\Repositories\Models\Holiday;

class HolidayRepository implements HolidayRepositoryInterface
{
    /** @var Holiday */
    protected $holiday;

    /**
     * @param Holiday $holiday
     */
    public function __construct(Holiday $holiday)
    {
        $this->holiday = $holiday;
    }

    /**
     * 当月分の祝日データを取得
     *
     * @param \Carbon\Carbon $dateFrom
     * @param \Carbon\Carbon $dateTo
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByDate(\Carbon\Carbon $dateFrom, \Carbon\Carbon $dateTo): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $holiday = $this->holiday->whereBetween('date', [$dateFrom, $dateTo])->orderBy('date', 'asc')->get();
            if (!$holiday) {
                $holiday = new Holiday();
            }
            return $holiday;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 全祝日取得処理
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $holiday = $this->holiday->orderBy('date', 'asc')->get();

            return $holiday;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 祝日データ取得
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getById(int $id): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $holiday = $this->holiday->where('id', '=', $id)->get();

            return $holiday;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 祝日データ登録
     *
     * @param array $requestArray
     * @return void
     */
    public function store(array $requestArray): void
    {
        try {
            $holiday = new Holiday;
            $holiday->date = $requestArray['holidayDate'];
            $holiday->name = $requestArray['holidayName'];
            $holiday->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 祝日データ編集
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $where = [ 'id' => $requestArray['holidayId'] ];
            $update_values  = [ 'date' => $requestArray['holidayDate'],
                                'name' => $requestArray['holidayName'],
                            ];

            $this->holiday->where($where)->update($update_values);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 祝日データ削除
     *
     * @param int $holidayId
     * @return void
     */
    public function delete(int $holidayId): void
    {
        try {
            $this->holiday->destroy($holidayId);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
