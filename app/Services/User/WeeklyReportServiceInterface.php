<?php

namespace App\Services\User;

interface WeeklyReportServiceInterface
{
    public function get(int $userId, int $weekNumber): \App\Repositories\Models\WeeklyReport;

    public function getAllUser(string $targetDate, int $weekNumber): \Illuminate\Database\Eloquent\Collection;

    public function store(array $requestArray): void;

    public function getUser(int $userId): \App\Repositories\Models\User;

    public function getWeeklyReportJSONAllUser(array $allUserWorkSchedulesJSON, \Illuminate\Database\Eloquent\Collection $allUserweeklyReports): array;

    public function getWeeklyReportJSON(array $workSchedulesJSON, \Illuminate\Database\Eloquent\Collection $weeklyReports): array;

    public function createTargetWeeks(int $weekNumber): array;
}
