<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_description extends Model
{
    //根据id获取语言的name
    public function languageName()
    {
       return $this->hasOne('App\Language','id','language_id');
    }
    public function isProduct()
    {
       return $this->belongsTo('App\Product','product_model','model');

    }
    public function categoryName()
    {
       return $this->belongsTo('App\Category','path','path');
    }
    public function getCate1()
    {
       return $this->belongsTo('App\Category','category_1','id');
    }
    public function getCate2()
    {
        return $this->belongsTo('App\Category','category_2','id');
    }
    public function getCate3()
    {
        return $this->belongsTo('App\Category','category_3','id');
    }
    public function getCate4()
    {
        return $this->belongsTo('App\Category','category_4','id');
    }
    public function getCommits()
    {
        return $this->hasMany('App\Commit','model','product_model');
    }
}
