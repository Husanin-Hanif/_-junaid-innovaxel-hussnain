<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reservation extends Model
{
    protected $table='reservation';
    protected $fillable=['user_id','showtime_id','seats'];
    protected $casts = [
        'seats' => 'array',
    ];
    use HasFactory;
    public function Showtime(){
        return $this->belongsTo(Showtime::class);
    }
    public function User(){
        return $this->belongsTo(User::class);

    }
}
