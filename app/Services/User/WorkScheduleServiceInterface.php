<?php

namespace App\Services\User;

interface WorkScheduleServiceInterface
{
    public function getByDate(int $userId, \Carbon\Carbon $dateFrom, \Carbon\Carbon $dateTo): \Illuminate\Database\Eloquent\Collection;

    public function getByWeekNumber(int $userId, string $weekNumber): \Illuminate\Database\Eloquent\Collection;

    public function getWorkScheduleAllUser(\Carbon\Carbon $dateFrom, \Carbon\Carbon $dateTo): \Illuminate\Database\Eloquent\Collection;

    public function getOldestWorkdateByUserId(int $userId): string;

    public function store(array $requestArray): void;
}
