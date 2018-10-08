<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Category_description;
use Illuminate\Http\Request;
use App\Language;

class CategoryController extends Controller
{
    public function index()
    {
       return view('categorys/index');
    }
    //添加全新分类
    public function add(Request $request)
    {

        if ($request->method() == 'GET'){
            $languages = Language::all();
            $brands = Brand::all();
            return view('categorys/add',compact('languages','brands'));
        }
        $language_id = $request->language_id;
        $name = $request->name;
        if ($request->parent_catrgory == null){
            $parent_id = 0;
        }else{
            $c = Category_description::query()
                ->where('name',$request->parent_catrgory)
                ->where('language_id',$language_id)
                ->first();
            if (!$c){
                return redirect()->back()->with('fail',1);
            }
            $parent_id = $c->findCategory->id;
        }

        $this->validate($request,[
            'name'=>'unique:category_descriptions|required',
//            'parent_id'=>'integer|required',
            'brand_id'=>'integer|required',
        ]);

        $category = new Category;
        $category->parent_id = $parent_id;
        $category->brand_id = $request->brand_id;
        $category->save();
        $category_description = new Category_description;
        $category_description->language_id = $language_id;
        $category_description->name = $name;
        $category->category_descriptions()->save($category_description);
         return redirect('categorys/add')->with('success',1);
    }
}
