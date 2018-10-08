<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Web_log extends Model
{
    public function getWeb()
    {
       return $this->belongsTo('App/Web','web_id','id');
    }
}
