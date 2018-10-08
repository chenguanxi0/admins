<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Common_commit extends Model
{
    protected $dates = ['created_at','replyTime'];

    public function getLan()
    {
        return $this->hasOne('App\Language','id','language_id');
    }
    public function getBrand()
    {
        return $this->hasOne('App\Brand','id','brand_id');
    }
}
