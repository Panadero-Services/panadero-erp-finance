<?php

namespace App\Models;

//use App\Models\User;
use App\Models\User;
use App\Models\Tag;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = ['id','title','content', 'user_id', 'tag_id', 'assignment','completed', 'public'];

    // Relations
   // public function user() { return $this->belongsTo(User::class,'user_id'); }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function tag() { return $this->hasOne(Tag::class, 'id', 'tag_id'); }

//      return $this->hasOne(child::class,'foreign_key','local_key');

}
