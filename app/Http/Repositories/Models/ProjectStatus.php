<?php

namespace App\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectStatus extends Model
{
    protected $table = 'project_statuses';

    protected $fillable = [
        'name',
        'is_deleted'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];
}
