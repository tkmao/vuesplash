<?php

namespace App\Repositories;

interface UserContractRepositoryInterface
{
    public function store(array $requestArray): void;

    public function edit(array $requestArray): void;

    public function delete(int $userContractId): void;
}
