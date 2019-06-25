<?php

namespace App\Repositories;

use App\Repositories\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    /** @var Category */
    protected $category;

    /**
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * 全PJ区分取得処理
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $categories = $this->category->orderBy('id', 'asc')->get();

            return $categories;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * PJ区分データ登録
     *
     * @param array $requestArray
     * @return void
     */
    public function store(array $requestArray): void
    {
        try {
            $category = new Category;
            $category->name = $requestArray['name'];
            $category->is_deleted = false;
            $category->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * PJ区分データ編集
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $where = [ 'id' => $requestArray['id'] ];
            $update_values  = [ 'name' => $requestArray['name'],
                                'is_deleted' => false,
                            ];

            $this->category->where($where)->update($update_values);
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * PJ区分データ削除
     *
     * @param int $categoryId
     * @return void
     */
    public function delete(int $categoryId): void
    {
        try {
            $where = [ 'id' => $categoryId ];
            $update_values  = [ 'is_deleted' => true ];

            $this->category->where($where)->update($update_values);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
