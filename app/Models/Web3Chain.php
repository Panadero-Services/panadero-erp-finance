<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Web3Chain extends Model
{
    use HasFactory;
    protected $fillable = ['id','title','chain_nr','icon'];
}
