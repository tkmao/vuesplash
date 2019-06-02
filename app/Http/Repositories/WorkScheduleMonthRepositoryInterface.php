<?php

namespace App\Repositories;

interface WorkScheduleMonthRepositoryInterface
{
    public function exists(array $requestArray): bool;

    public function store(array $requestArray): void;
}
