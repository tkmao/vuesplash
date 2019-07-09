<?php

namespace App\Services\User;

interface UserServiceInterface
{
    public function find(int $userId): \App\Repositories\Models\User;

    public function all(bool $onlyActive): \Illuminate\Database\Eloquent\Collection;

    public function store(array $requestArray): void;

    public function edit(array $requestArray): void;

    public function delete(int $userId): void;
}
