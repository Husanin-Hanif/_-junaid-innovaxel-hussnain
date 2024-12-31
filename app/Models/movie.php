<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class movie extends Model
{
    use HasFactory;
    protected $table = 'movie';
    protected $fillable=['title','description','genre'];


    public function Showtime(){
        return $this->hasMany(Showtime::class);
    }

}
