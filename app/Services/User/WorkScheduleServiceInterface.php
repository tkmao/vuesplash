<?php

namespace App\Services\User;

interface WorkScheduleServiceInterface
{
    public function getWorkSchedule(int $userId, \Carbon\Carbon $dateFrom, \Carbon\Carbon $dateTo): \Illuminate\Database\Eloquent\Collection;

    public function getWorkScheduleAllUser(\Carbon\Carbon $dateFrom, \Carbon\Carbon $dateTo): \Illuminate\Database\Eloquent\Collection;

    public function store(array $requestArray): void;
}
