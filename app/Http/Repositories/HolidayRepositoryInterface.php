<?php

namespace App\Repositories;

interface HolidayRepositoryInterface
{
    public function findByDate(\Carbon\Carbon $dateFrom, \Carbon\Carbon $dateTo): \Illuminate\Database\Eloquent\Collection;

    public function all(): \Illuminate\Database\Eloquent\Collection;

    public function getById(int $id): \Illuminate\Database\Eloquent\Collection;

    public function store(array $requestArray): void;

    public function edit(array $requestArray): void;

    public function delete(int $holidayId): void;
}
