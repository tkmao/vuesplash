<?php

namespace App\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $table = 'user_types';

    protected $fillable = [
        'name'
    ];

    public $sortable = [
        'name'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];
}
