<?php

namespace App\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'is_deleted'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];
}
