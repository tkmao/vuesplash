<?php

namespace App\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';

    protected $fillable = [
        'code',
        'name',
        'category_id',
        'company_id',
        'user_id',
        'status_id',
        'is_deleted',
        'category',
        'company',
        'user',
        'projectStatus'
    ];

    public $sortable = [
        'code',
        'category_id',
        'company_id',
        'user_id',
        'status_id',
        'category',
        'company',
        'user',
        'projectStatus'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo('App\Repositories\Models\Category', 'category_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo('App\Repositories\Models\Company', 'company_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Repositories\Models\User', 'user_id', 'id');
    }

    public function projectStatus()
    {
        return $this->belongsTo('App\Repositories\Models\ProjectStatus', 'status_id', 'id');
    }
}
