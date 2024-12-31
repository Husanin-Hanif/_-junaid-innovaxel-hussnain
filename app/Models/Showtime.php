<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Showtime extends Model
{
    protected $table='showtime';
    protected $fillable=['movie_id','date','firsttime','secondtime','capacity','price'];
    use HasFactory;
    public function movie(){
        return $this->belongsTo(movie::class);
    }
    public function reservation(){
        return $this->hasMany(reservation::class);
    }


     public function getFirsttimeAttribute($value)
     {
         return Carbon::createFromFormat('H:i:s', $value)->format('h:i A');
     }


     public function getSecondtimeAttribute($value)
     {
         return Carbon::createFromFormat('H:i:s', $value)->format('h:i A');
     }


}
