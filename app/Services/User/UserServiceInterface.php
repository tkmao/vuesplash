<?php

namespace App\Services\User;

interface UserServiceInterface
{
    public function getUser(int $userId): \Illuminate\Database\Eloquent\Collection;

    public function getAllUser(): \Illuminate\Database\Eloquent\Collection;

    public function store(array $requestArray): void;

    public function edit(array $requestArray): void;

    public function delete(int $userId): void;
}
