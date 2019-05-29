<?php

namespace App\Repositories;

interface ProjectWorkRepositoryInterface
{
    public function find($id);

    public function store(array $requestArray): void;
}
