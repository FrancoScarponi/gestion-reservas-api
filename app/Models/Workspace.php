<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'location'
    ];

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }
}
