<?php

namespace App\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class UserContract extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'user_contracts';

    protected $fillable = [
        'user_id',
        'usertype_id',
        'workingtime_type',
        'worktime_day',
        'maxworktime_month',
        'workingtime_min',
        'workingtime_max',
        'startdate',
        'enddate'
    ];

    public $sortable = [
        'user_id',
        'usertype_id',
        'workingtime_type',
        'worktime_day',
        'maxworktime_month',
        'workingtime_min',
        'workingtime_max',
        'startdate',
        'enddate',
        'userType'
    ];

    public function userType()
    {
        return $this->belongsTo('App\Repositories\Models\UserType', 'usertype_id', 'id');
    }
}
