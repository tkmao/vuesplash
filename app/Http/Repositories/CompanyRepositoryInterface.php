<?php

namespace App\Repositories;

interface CompanyRepositoryInterface
{
    public function all(bool $onlyActive): \Illuminate\Database\Eloquent\Collection;

    public function store(array $requestArray): void;

    public function edit(array $requestArray): void;

    public function delete(int $companyId): void;
}
