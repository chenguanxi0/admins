<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Web_product extends Model
{
    protected $fillable = ['web_id','path','language_id','model','created_at','updated_at'];
    
    public function getPath()
    {
       return $this->hasOne('App\Category','path','path');
    }
    public function getLanguage()
    {
        return $this->hasOne('App\Language','id','language_id');
    }
    public function getProduct()
    {
        return $this->hasOne('App\Product','model','model');
    }
    public function getProducts()
    {
        return $this->hasMany('App\Product_description','product_model','model');
    }
    public function getWeb()
    {
        return $this->belongsTo('App\Web','web_id','id');
    }
}
