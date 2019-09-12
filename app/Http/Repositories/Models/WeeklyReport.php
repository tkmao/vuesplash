<?php

namespace App\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyReport extends Model
{
    protected $table = 'weekly_reports';

    protected $fillable = [
        'user_id',
        'week_number',
        'project_id',
        'nextweek_schedule',
        'thismonth_dayoff',
        'site_information',
        'opinion',
        'is_subumited'
    ];

    public $sortable = [
        'user_id',
        'week_number',
        'project_id',
        'is_subumited'
    ];

    protected $casts = [
        'is_subumited' => 'boolean',
    ];

    public function project()
    {
        return $this->hasOne('App\Repositories\Models\Project', 'id', 'project_id');
    }
}
