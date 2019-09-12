<?php

namespace App\Services\User;

interface UserContractServiceInterface
{
    public function store(array $requestArray): void;

    public function edit(array $requestArray): void;

    public function delete(int $userContractId): void;
}
