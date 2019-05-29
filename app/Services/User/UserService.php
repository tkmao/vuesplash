<?php

namespace App\Services\User;

use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class UserService implements UserServiceInterface
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepositoryInterface;

    /**
     * @param App\Repositories\UserRepositoryInterface  $userRepositoryInterface  The user repository
     */
    public function __construct(
        UserRepositoryInterface $userRepositoryInterface
    ) {
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    /**
     * ユーザデータ取得
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return $this->userRepositoryInterface->getById($userId);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 全ユーザデータ取得
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUser(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return $this->userRepositoryInterface->all();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザデータ登録
     *
     * @param array $requestArray
     * @return void
     */
    public function store(array $requestArray): void
    {
        try {
            $this->userRepositoryInterface->store($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザデータ修正
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $this->userRepositoryInterface->edit($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザデータ削除
     *
     * @param int $userId
     * @return void
     */
    public function delete(int $userId): void
    {
        try {
            $this->userRepositoryInterface->delete($userId);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
