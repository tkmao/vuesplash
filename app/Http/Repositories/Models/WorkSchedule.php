<?php

namespace App\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    protected $table = 'work_schedules';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'workdate',
        'week_number',
        'detail',
        'starttime_hh',
        'starttime_mm',
        'endtime_hh',
        'endtime_mm',
        'breaktime',
        'breaktime_midnight',
        'is_paid_holiday',
    ];

    /** JSONに含める属性 */
    protected $visible = [
        'id',
        'user_id',
        'workdate',
        'week_number',
        'detail',
        'starttime_hh',
        'starttime_mm',
        'endtime_hh',
        'endtime_mm',
        'breaktime',
        'breaktime_midnight',
        'is_paid_holiday',
        'projectWork',
    ];

    protected $casts = [
        'is_paid_holiday' => 'boolean'
    ];

    public function projectWork()
    {
        return $this->hasMany('App\Repositories\Models\ProjectWork', 'work_schedule_id');
    }

    public function holiday()
    {
        return $this->hasone('App\Repositories\Models\Holiday', 'date', 'workdate');
    }
}
