<?php

namespace App\Services\User;

use App\Repositories\UserContractRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class UserService implements UserServiceInterface
{
    /**
     * @var UserContractRepositoryInterface
     * @var UserRepositoryInterface
     */
    protected $userContractRepositoryInterface;
    protected $userRepositoryInterface;

    /**
     * @param App\Repositories\UserContractRepositoryInterface  $userContractRepositoryInterface  The user contract repository
     * @param App\Repositories\UserRepositoryInterface  $userRepositoryInterface  The user repository
     */
    public function __construct(
        UserContractRepositoryInterface $userContractRepositoryInterface,
        UserRepositoryInterface $userRepositoryInterface
    ) {
        $this->userContractRepositoryInterface = $userContractRepositoryInterface;
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    /**
     * ユーザデータ取得
     *
     * @param int $userId
     * @return \App\Repositories\Models\User
     */
    public function find(int $userId): \App\Repositories\Models\User
    {
        try {
            return $this->userRepositoryInterface->find($userId);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 全ユーザデータ取得
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(): \Illuminate\Database\Eloquent\Collection
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
            $requestArray['user_contract'][0]['user_id'] = $this->userRepositoryInterface->store($requestArray);
            $this->userContractRepositoryInterface->store($requestArray['user_contract'][0]);
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
