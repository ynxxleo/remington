<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotContract extends Model
{
    use HasFactory;

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'amount' => 'decimal:2',
        'profit' => 'decimal:2',
        'status' => 'integer',
        'result' => 'integer',
    ];
}
