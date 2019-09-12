<?php

namespace App\Services\User;

use App\Repositories\UserContractRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class UserContractService implements UserContractServiceInterface
{
    /**
     * @var UserContractRepositoryInterface
     */
    protected $userContractRepositoryInterface;

    /**
     * @param App\Repositories\UserContractRepositoryInterface  $userContractRepositoryInterface  The user contract repository
     */
    public function __construct(
        UserContractRepositoryInterface $userContractRepositoryInterface
    ) {
        $this->userContractRepositoryInterface = $userContractRepositoryInterface;
    }

    /**
     * ユーザ契約データ登録
     *
     * @param array $requestArray
     * @return void
     */
    public function store(array $requestArray): void
    {
        try {
            $this->userContractRepositoryInterface->store($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザ契約データ修正
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $this->userContractRepositoryInterface->edit($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザ契約データ削除
     *
     * @param int $userContractId
     * @return void
     */
    public function delete(int $userContractId): void
    {
        try {
            $this->userContractRepositoryInterface->delete($userContractId);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
