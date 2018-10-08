<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $fillable = [
        'model','special_price','price','costPrice','freight','image','category_id','created_at','updated_at','change','sureChange'
    ];

    //查询同一产品的多语言信息
    public function product_description()
    {
       return $this->hasMany('App\Product_description','product_model','model');
    }

    //筛选出英文的产品信息
    public function language_description_1($language_id)
    {
        return $this->product_description()->where('language_id',$language_id);
    }

    public function category_name()
    {
        return $this->hasOne('App\Category','id','category_id');
    }
    public function getLog()
    {
        return $this->hasOne('App\Product_log','model','model');
    }
}
