<?php

namespace App\Repositories;

use App\Repositories\Models\UserContract;

class UserContractRepository implements UserContractRepositoryInterface
{
    /** @var UserContract */
    protected $userContract;

    /**
     * @param UserContract $userContract
     */
    public function __construct(UserContract $userContract)
    {
        $this->userContract = $userContract;
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
            $userContract = new UserContract;
            $userContract->user_id = $requestArray['user_id'];
            $userContract->usertype_id = $requestArray['usertype_id'];
            $userContract->workingtime_type = $requestArray['workingtime_type'];
            $userContract->worktime_day = $requestArray['worktime_day'];
            $userContract->maxworktime_month = $requestArray['maxworktime_month'];
            $userContract->workingtime_min = $requestArray['workingtime_min'];
            $userContract->workingtime_max = $requestArray['workingtime_max'];
            $userContract->startdate = $requestArray['startdate'];
            $userContract->enddate = $requestArray['enddate'];
            $userContract->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザ契約データ編集
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $where = [ 'id' => $requestArray['id'] ];
            $update_values  = [ 'user_id' => $requestArray['user_id'],
                                'usertype_id' => $requestArray['usertype_id'],
                                'workingtime_type' => $requestArray['workingtime_type'],
                                'worktime_day' => $requestArray['worktime_day'],
                                'maxworktime_month' => $requestArray['maxworktime_month'],
                                'workingtime_min' => $requestArray['workingtime_min'],
                                'workingtime_max' => $requestArray['workingtime_max'],
                                'startdate' => $requestArray['startdate'],
                                'enddate' => $requestArray['enddate'],
                            ];

            $this->userContract->where($where)->update($update_values);
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
            $this->userContract->where('id', $userContractId)->delete();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
