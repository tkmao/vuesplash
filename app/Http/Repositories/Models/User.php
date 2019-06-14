<?php

namespace App\Repositories\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'hiredate',
        'paid_holiday',
        'is_admin',
        'is_deleted'
    ];

    /** JSONに含める属性 */
    protected $visible = [
        'id',
        'name',
        'email',
        'hiredate',
        'paid_holiday',
        'is_admin',
        'is_deleted',
        'workSchedule',
        'weeklyReport',
        'userContract'
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_deleted' => 'boolean'
    ];

    /**
     * リレーションシップ - photosテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    /*
    public function photos()
    {
        return $this->hasMany('App\Photo');
    }
    */

    public function workSchedule()
    {
        return $this->hasMany('App\Repositories\Models\WorkSchedule', 'user_id');
    }

    public function weeklyReport()
    {
        return $this->hasOne('App\Repositories\Models\WeeklyReport', 'user_id');
    }

    public function userContract()
    {
        return $this->hasMany('App\Repositories\Models\UserContract', 'user_id');
    }
}
