<?php

namespace App\Services\User;

use App\Repositories\CompanyRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class CompanyService implements CompanyServiceInterface
{
    /**
     * @var CompanyRepositoryInterface
     */
    protected $companyRepositoryInterface;

    /**
     * @param App\Repositories\CompanyRepositoryInterface  $companyRepositoryInterface  The company repository
     */
    public function __construct(
        CompanyRepositoryInterface $companyRepositoryInterface
    ) {
        $this->companyRepositoryInterface = $companyRepositoryInterface;
    }

    /**
     * 全企業データ取得
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return $this->companyRepositoryInterface->all();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 企業データ登録
     *
     * @param array $requestArray
     * @return void
     */
    public function store(array $requestArray): void
    {
        try {
            $this->companyRepositoryInterface->store($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 企業データ修正
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $this->companyRepositoryInterface->edit($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 企業データ削除
     *
     * @param int $companyId
     * @return void
     */
    public function delete(int $companyId): void
    {
        try {
            $this->companyRepositoryInterface->delete($companyId);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
