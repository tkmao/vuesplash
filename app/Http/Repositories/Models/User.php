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
        'usertype_id',
        'workingtime_type',
        'worktime_day',
        'maxworktime_month',
        'workingtime_min',
        'workingtime_max',
        'hiredate',
        'paid_holiday',
        'is_admin',
        'is_deleted',
    ];

    /** JSONに含める属性 */
    protected $visible = [
        'id',
        'name',
        'email',
        'usertype_id',
        'workingtime_type',
        'worktime_day',
        'maxworktime_month',
        'workingtime_min',
        'workingtime_max',
        'hiredate',
        'paid_holiday',
        'is_admin',
        'is_deleted',
        'workSchedule',
        'weeklyReport',
        'userType'
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

    public function userType()
    {
        return $this->belongsTo('App\Repositories\Models\UserType', 'usertype_id', 'id');
    }
}
