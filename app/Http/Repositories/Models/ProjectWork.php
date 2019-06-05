<?php

namespace App\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectWork extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'project_works';

    protected $fillable = [
        'project_id',
        'work_schedule_id',
        'worktime',
        'project'
    ];

    public $sortable = [
        'project_id',
        'work_schedule_id',
        'worktime',
        'project'
    ];

    public function workSchedule()
    {
        return $this->belongsTo('App\Repositories\Models\WorkSchedule', 'work_schedule_id', 'id');
    }

    public function project()
    {
        return $this->belongsTo('App\Repositories\Models\Project', 'project_id', 'id');
    }
}
