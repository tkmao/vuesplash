<?php

namespace App\Services\User;

interface CompanyServiceInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection;

    public function store(array $requestArray): void;

    public function edit(array $requestArray): void;

    public function delete(int $companyId): void;
}
