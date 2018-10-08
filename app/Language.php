<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'name','code'
    ];

    public function products()
    {
        return $this->belongsToMany('App\Product');
    }



}
