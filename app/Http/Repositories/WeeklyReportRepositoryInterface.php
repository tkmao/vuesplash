<?php

namespace App\Repositories;

interface WeeklyReportRepositoryInterface
{
    public function getWeeklyReport(int $userId, int $weekNumber): \App\Repositories\Models\WeeklyReport;

    public function store(array $requestArray): void;

    public function edit(array $requestArray): void;
}
