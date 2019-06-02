<?php

namespace App\Repositories;

interface WorkScheduleRepositoryInterface
{
    public function getWorkSchedule(int $userId, \Carbon\Carbon $dateFrom, \Carbon\Carbon $dateTo): \Illuminate\Database\Eloquent\Collection;

    public function getWorkScheduleByUserIdWeekNumber(int $userId, int $weekNumber): \Illuminate\Database\Eloquent\Collection;

    public function getOldestWorkdate(): string;

    public function store(array $workSchedules): array;
}
