<?php

namespace App\Services\User;

interface HolidayServiceInterface
{
    public function getAllHoliday(): \Illuminate\Database\Eloquent\Collection;

    public function store(array $requestArray): void;

    public function edit(array $requestArray): void;

    public function delete(int $holidayId): void;
}
