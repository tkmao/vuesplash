<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function find($id): \App\Repositories\Models\User;

    public function all(): \Illuminate\Database\Eloquent\Collection;

    public function store(array $requestArray): void;

    public function edit(array $requestArray): void;

    public function delete(int $userId): void;

    public function getWorkScheduleByWeekNumber(int $weekNumber): \Illuminate\Database\Eloquent\Collection;

    public function getWeeklyReportByWeekNumber(int $weekNumber): \Illuminate\Database\Eloquent\Collection;

    public function getWorkSchedule(\Carbon\Carbon $dateFrom, \Carbon\Carbon $dateTo): \Illuminate\Database\Eloquent\Collection;
}
