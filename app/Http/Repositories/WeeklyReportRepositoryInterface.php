<?php

namespace App\Repositories;

interface WeeklyReportRepositoryInterface
{
    public function get(int $userId, int $weekNumber): \App\Repositories\Models\WeeklyReport;

    public function getAllUser(int $weekNumber): \Illuminate\Database\Eloquent\Collection;

    public function store(array $requestArray): void;

    public function edit(array $requestArray): void;
}
