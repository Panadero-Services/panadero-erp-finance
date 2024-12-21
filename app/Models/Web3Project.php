<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Web3Project extends Model
{
    use HasFactory;
    protected $fillable = ['id','title','content','color'];
}
