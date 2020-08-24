<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'parent_id',
        'text',
        'memo',
        'start_date_time',
        'stop_date_time',
        'deadline_date',
        'deadline_second',
        'run_start_date_time',
        'run_stop_date_time',
        'runtime_second',
        'display_sequence',
        'status',
    ];
}
