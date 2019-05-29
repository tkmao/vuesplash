<?php

namespace App\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'holidays';

    protected $fillable = [
        'date',
        'name'
    ];

    public $sortable = [
        'date'
    ];
}
