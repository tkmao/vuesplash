<?php

namespace App\Repositories;

interface WorkScheduleRepositoryInterface
{
    public function getByDate(int $userId, \Carbon\Carbon $dateFrom, \Carbon\Carbon $dateTo): \Illuminate\Database\Eloquent\Collection;

    public function getByWeekNumber(int $userId, string $weekNumber): \Illuminate\Database\Eloquent\Collection;

    public function getOldestWorkdate(): string;

    public function getOldestWorkdateByUserId(int $userId): string;

    public function store(array $workSchedules): array;
}
