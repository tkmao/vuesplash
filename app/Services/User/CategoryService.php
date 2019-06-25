<?php

namespace App\Services\User;

use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class CategoryService implements CategoryServiceInterface
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepositoryInterface;

    /**
     * @param App\Repositories\CategoryRepositoryInterface  $categoryRepositoryInterface  The category repository
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepositoryInterface
    ) {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
    }

    /**
     * 全ユーザタイプデータ取得
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return $this->categoryRepositoryInterface->all();
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
            $this->categoryRepositoryInterface->store($requestArray);
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
            $this->categoryRepositoryInterface->edit($requestArray);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザタイプデータ削除
     *
     * @param int $categoryId
     * @return void
     */
    public function delete(int $categoryId): void
    {
        try {
            $this->categoryRepositoryInterface->delete($categoryId);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
