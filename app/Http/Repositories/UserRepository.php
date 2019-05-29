<?php

namespace App\Repositories;

use App\Repositories\Models\User;

class UserRepository implements UserRepositoryInterface
{
    /** @var User */
    protected $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * ユーザ情報取得
     *
     * @param int $id
     * @return \App\Repositories\Models\User
     */
    public function find($id): \App\Repositories\Models\User
    {
        try {
            $user = $this->user->find($id);
            if (!$user) {
                $user = new User();
            }

            return $user;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 全ユーザ取得処理
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $user = $this->user->with(['userType'])->where('is_deleted', false)->get();

            return $user;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザデータ取得
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getById(int $id): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $user = $this->user->where('id', '=', $id)->get();

            return $user;
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
            $user = new User;
            $user->name = $requestArray['userName'];
            $user->email = $requestArray['userEmail'];
            $user->password = bcrypt($requestArray['userPassword']);
            $user->usertype_id = $requestArray['userTypeId'];
            $user->workingtime_type = $requestArray['workingtimeType'];
            $user->worktime_day = (isset($requestArray['worktimeDay']) ? $requestArray['worktimeDay'] : null);
            $user->maxworktime_month = (isset($requestArray['maxWorktimeMonth']) ? $requestArray['maxWorktimeMonth'] : null);
            $user->workingtime_min = (isset($requestArray['workingtimeMin']) ? $requestArray['workingtimeMin'] : null);
            $user->workingtime_max = (isset($requestArray['workingtimeMax']) ? $requestArray['workingtimeMax'] : null);
            $user->paid_holiday = $requestArray['paidHoliday'];
            $user->hiredate = $requestArray['hiredate'];
            $user->is_admin = $requestArray['userIsAdmin'];
            $user->is_deleted = false;
            $user->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ユーザデータ編集
     *
     * @param array $requestArray
     * @return void
     */
    public function edit(array $requestArray): void
    {
        try {
            $where = [ 'id' => $requestArray['userId'] ];
            $update_values  = [ 'name' => $requestArray['userName'],
                                'email' => $requestArray['userEmail'],
                                'usertype_id' => $requestArray['userTypeId'],
                                'workingtime_type' => $requestArray['workingtimeType'],
                                'worktime_day' => (isset($requestArray['worktimeDay']) ? $requestArray['worktimeDay'] : null),
                                'maxworktime_month' => (isset($requestArray['maxWorktimeMonth']) ? $requestArray['maxWorktimeMonth'] : null),
                                'workingtime_min' => (isset($requestArray['workingtimeMin']) ? $requestArray['workingtimeMin'] : null),
                                'workingtime_max' => (isset($requestArray['workingtimeMax']) ? $requestArray['workingtimeMax'] : null),
                                'paid_holiday' => $requestArray['paidHoliday'],
                                'hiredate' => $requestArray['hiredate'],
                                'is_admin' => $requestArray['userIsAdmin'],
                                'is_deleted' => false,
                            ];

            $this->user->where($where)->update($update_values);
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
            $where = [ 'id' => $userId ];
            $update_values  = [ 'is_deleted' => true ];

            $this->user->where($where)->update($update_values);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 全ユーザ勤務表情報取得（週単位）
     *
     * @param int $weekNumber
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWorkScheduleByWeekNumber(int $weekNumber): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $user = $this->user
                            ->with(['workSchedule' => function ($query) use ($weekNumber) {
                                $query->with(['projectWork.project', 'holiday'])
                                      ->where('week_number', '=', $weekNumber)
                                      ->orderBy('workdate', 'asc');
                            }])
                            ->where('is_deleted', false)->orderBy('id', 'asc')->get();

            if (!$user) {
                $user = new User();
            }

            return $user;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 全ユーザ勤務表情報取得
     *
     * @param int $weekNumber
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWeeklyReportByWeekNumber(int $weekNumber): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $user = $this->user
                            ->with(['weeklyReport' => function ($query) use ($weekNumber) {
                                $query->with(['project'])
                                      ->where('week_number', '=', $weekNumber)
                                      ->get();
                            }])
                            ->where('is_deleted', false)->orderBy('id', 'asc')->get();

            if (!$user) {
                $user = new User();
            }

            return $user;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 全ユーザ勤務表情報取得（日付指定）
     *
     * @param \Carbon\Carbon $dateFrom
     * @param \Carbon\Carbon $dateTo
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWorkSchedule(\Carbon\Carbon $dateFrom, \Carbon\Carbon $dateTo): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $user = $this->user
                            ->with(['workSchedule' => function ($query) use ($dateFrom, $dateTo) {
                                $query->with(['projectWork.project', 'holiday'])
                                      ->whereBetween('workdate', [$dateFrom, $dateTo])
                                      ->orderBy('workdate', 'asc');
                            }])
                            ->where('is_deleted', false)->orderBy('id', 'asc')->get();

            if (!$user) {
                $user = new User();
            }

            return $user;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
