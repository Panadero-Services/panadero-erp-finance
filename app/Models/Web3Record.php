<?php

namespace App\Models;

//use App\Models\User;
//use App\Models\User;
use App\Models\Web3Project;
use App\Models\Web3Type;
use App\Models\Web3Chain;
use App\Models\Web3RecordLine;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Web3Record extends Model
{
    use HasFactory;
    protected $fillable = ['id','title','web3type_id', 'web3project_id', 'web3chain_id', 'tags', 'address', 'url', 'abi', 'description', 'json', 'user_id', 'is_active', 'is_live', 'completed', 'archived'];

    // Relations
   // public function user() { return $this->belongsTo(User::class,'user_id'); }
    public function web3project() { return $this->belongsTo(Web3Project::class); }

    // return $this->hasOne(child::class,'foreign_key','local_key');
    public function web3type() { 
        // todo .. manipulated related fields!
        $r = $this->hasOne(Web3Type::class, 'id', 'web3type_id'); 
        //$r->newField = "ExtraFieldTest";
        //$r->color="blue";
        return $r; 
    }


    public function web3chain() { return $this->hasOne(Web3Chain::class, 'id', 'web3chain_id'); }
    public function web3recordlines() { return $this->hasMany(Web3RecordLine::class, 'web3record_id', 'id'); }

}
