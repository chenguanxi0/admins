<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categorys';
    
    public function findParent()
    {
        return $this->belongsTo('App\Category','parent_id','id');
    }
    public function getChild()
    {
        return $this->hasMany('App\Category','parent_id','id');
    }
    
    public function getProduct()
    {
        return $this->hasMany('App\Product_description','path','path');
    }
    public function getLan()
    {
        return $this->hasOne('App\Language','id','language_id');
    }
}
