<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    public function getCategorys()
    {
       return $this->hasMany('App\Category','brand_id','id');
    }
}
