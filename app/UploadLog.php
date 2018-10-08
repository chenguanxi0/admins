<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UploadLog extends Model
{
    protected $fillable = ['language_id','brand_id','fileName','created_at','type'];
    public function getLan()
    {
        return $this->hasOne('App\Language','id','language_id');
    }
    public function getBrand()
    {
        return $this->hasOne('App\Brand','id','brand_id');
    }
}
