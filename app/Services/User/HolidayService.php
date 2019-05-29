<?php

namespace App\Services\User;

use App\Repositories\HolidayRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class HolidayService implements HolidayServiceInterface
{
    /**
     * @var HolidayRepositoryInterface
     */
    protected $holidayRepositoryInterface;

    /**
     * @param App\Repositories\HolidayRepositoryInterface  $holidayRepositoryInterface  The holiday repository
     */
    public function __construct(
        HolidayRepositoryInterface $holidayRepositoryInterface
    ) {
        $this->holidayRepositoryInterface = $holidayRepositoryInterface;
    }

    /**
     * 祝日データ取得
     *
     * @param int $holidayId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getHoliday(int $holidayId): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return $this->holidayRepositoryInterface->getById($holidayId);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 全祝日データ取得
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllHoliday(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return $this->holidayRepositoryInterface->all();
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
            $this->holidayRepositoryInterface->store($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 祝日データ修正
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $this->holidayRepositoryInterface->edit($requestArray);
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
            $this->holidayRepositoryInterface->delete($holidayId);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
