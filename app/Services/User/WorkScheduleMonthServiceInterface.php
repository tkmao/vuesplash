<?php

namespace App\Services\User;

interface WorkScheduleMonthServiceInterface
{
    public function exists(array $requestArray): bool;
}
