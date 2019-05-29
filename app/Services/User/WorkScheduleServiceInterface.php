<?php

namespace App\Services\User;

interface WorkScheduleServiceInterface
{
    public function get(int $userId): \Illuminate\Database\Eloquent\Collection;

    public function store(array $requestArray): void;

    public function edit(array $requestArray): void;

    public function delete(int $userId): void;
}
