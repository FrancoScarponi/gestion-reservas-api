<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'workspace_id',
        'date',
        'start_time',
        'end_time',
        'status',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function Workspace(){
        return $this->belongsTo(Workspace::class);
    }
}
