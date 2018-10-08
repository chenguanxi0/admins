<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Web extends Model
{
    protected $fillable = ['hasSet','zip'];
    public function getLan()
    {
       return $this->hasOne('App\Language','id','language_id');
    }
    public function getBrand()
    {
        return $this->hasOne('App\Brand','id','brand_id');
    }
    public function getWebPproducts()
    {
        return $this->hasMany('App\Web_product','web_id','id');
    }
    public function getWebLogs()
    {
        return $this->hasMany('App\Web_log','web_id','id');
    }
}
