<?php

namespace App\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class WorkScheduleMonth extends Model
{
    protected $table = 'work_schedule_months';

    protected $fillable = [
        'user_id',
        'yearmonth',
        'is_subumited'
    ];

    public $sortable = [
        'user_id',
        'yearmonth',
        'is_subumited'
    ];

    protected $casts = [
        'is_subumited' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo('App\Repositories\Models\User', 'user_id', 'id');
    }
}
