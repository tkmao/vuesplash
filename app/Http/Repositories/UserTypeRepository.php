<?php

namespace App\Repositories;

use App\Repositories\Models\UserType;

class UserTypeRepository implements UserTypeRepositoryInterface
{
    /** @var UserType */
    protected $userType;

    /**
     * @param UserType $userType
     */
    public function __construct(UserType $userType)
    {
        $this->userType = $userType;
    }

    /**
     * 全ユーザタイプ取得処理
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $userType = $this->userType->orderBy('date', 'asc')->get();

            return $userType;
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
            $userType = new UserType;
            $userType->name = $requestArray['name'];
            $userType->is_deleted = $requestArray['is_deleted'];
            $userType->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザタイプデータ編集
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $where = [ 'id' => $requestArray['id'] ];
            $update_values  = [ 'name' => $requestArray['name'],
                                'is_deleted' => $requestArray['is_deleted'],
                            ];

            $this->userType->where($where)->update($update_values);
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
            $this->userType->destroy($userTypeId);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
