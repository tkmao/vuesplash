<?php

namespace App\Services\User;

use App\Repositories\UserTypeRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class UserTypeService implements UserTypeServiceInterface
{
    /**
     * @var UserTypeRepositoryInterface
     */
    protected $userTypeRepositoryInterface;

    /**
     * @param App\Repositories\UserTypeRepositoryInterface  $userTypeRepositoryInterface  The userType repository
     */
    public function __construct(
        UserTypeRepositoryInterface $userTypeRepositoryInterface
    ) {
        $this->userTypeRepositoryInterface = $userTypeRepositoryInterface;
    }

    /**
     * 全ユーザタイプデータ取得
     *
     * @param bool $onlyActive
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(bool $onlyActive): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return $this->userTypeRepositoryInterface->all($onlyActive);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザタイプデータ登録
     *
     * @param array $requestArray
     * @return void
     */
    public function store(array $requestArray): void
    {
        try {
            $this->userTypeRepositoryInterface->store($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザタイプデータ修正
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $this->userTypeRepositoryInterface->edit($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザタイプデータ削除
     *
     * @param int $userTypeId
     * @return void
     */
    public function delete(int $userTypeId): void
    {
        try {
            $this->userTypeRepositoryInterface->delete($userTypeId);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
