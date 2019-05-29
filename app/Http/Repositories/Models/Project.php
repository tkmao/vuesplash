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
        'is_deleted'
    ];

    public $sortable = [
        'code',
        'category_id',
        'company_id',
        'user_id',
        'status_id'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function category()
    {
        return $this->hasOne('App\Repositories\Models\Category', 'id', 'category_id');
    }

    public function company()
    {
        return $this->hasOne('App\Repositories\Models\Company', 'id', 'company_id');
    }

    public function user()
    {
        return $this->hasOne('App\Repositories\Models\User', 'id', 'user_id');
    }

    public function projectStatus()
    {
        return $this->hasOne('App\Repositories\Models\ProjectStatus', 'id', 'status_id');
    }
}
