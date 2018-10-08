<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Commit extends Model
{
    protected $fillable = ['id','model','language_id','content','reply','replyTime','img','username','created_at','updated_at'];

    public function getLan()
    {
        return $this->hasOne('App\Language','id','language_id');
    }
}
