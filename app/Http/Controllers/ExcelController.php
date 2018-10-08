<?php

namespace App\Http\Controllers;

use App\Category;
use App\Category_description;
use App\Product;
use App\Product_description;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;


class ExcelController extends Controller
{
    //Excel文件导出功能 By Laravel学院
    public function export(){

//        echo chr(0xEF).chr(0xBB).chr(0xBF);   //只针对csv文件
        $cellData = [
            ['学号','姓名','成绩'],
            ['10001','AAAAA','99'],
            ['10002','BBBBB','92'],
            ['10003','CCCCC','95'],
            ['10004','DDDDD','89'],
            ['10005','EEEEE','96'],
        ];
        Excel::create('学生成绩',function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xlsx');
    }



    //Excel文件导入功能
    public function import(Request $request){

        //接受数据
        if(!$request->hasFile('file')){
            exit('上传文件为空！');
        }
        $file = $_FILES;
        $excel_file_path = $file['file']['tmp_name'];
        $excel = App::make('excel');//excel类
        $excel->load($excel_file_path, function($reader) use( &$res ) {
            $reader = $reader->getSheet(0);
            $res = $reader->toArray();
            unset($res[0]);//去除表头
        });


        //categorys表操作
        foreach ($res as $k=>$v){
            if ($v[5]){
                // 1. 先判断该分类是否存在   --分类1
                $name = Category::where('name',$v[5])->get();
                if (!$name->first()){  //该name不存在,插入此条数据
                    $category = new Category;
                    $category->parent_id = 0;
                    $category->name = $v[5];
                    $category->brand_id = 1;
                    $category->save();       //--分类1

                    if ($v[6]){
                        $category_2 = new Category;
                        $name_2 = Category::where('name',$v[5])->get()->first()->name;
                        $name_2 = Category::where('name',$name_2)->get();

                        $category_2->parent_id = $name_2->first()->id;
                        $category_2->name = $v[6];
                        $category_2->brand_id = $name_2->first()->brand_id;
                        $category_2->save();       //--分类2
                        if ($v[7]){

                            $category_3 = new Category;
                            $name_3 = Category::where('name',$v[6])->get()->first()->name;
                            $name_3 = Category::where('name',$name_3)->get();

                            $category_3->parent_id = $name_3->first()->id;
                            $category_3->name = $v[7];
                            $category_3->brand_id = $name_3->first()->brand_id;
                            $category_3->save();       //--分类3
                            if ($v[8]){

                                $category_4 = new Category;
                                $name_4 = Category::where('name',$v[7])->get()->first()->name;
                                $name_4 = Category::where('name',$name_4)->get();

                                $category_4->parent_id = $name_4->first()->id;
                                $category_4->name = $v[8];
                                $category_4->brand_id = $name_4->first()->brand_id;
                                $category_4->save();       //--分类4

                            }
                        }
                    }



                }else{
                    // 2. 该分类已经存在 查找后面分类  --分类1

                    if($v[6]){ //判断上传的表当中是否有2级分类

                        // 先判读二级分类是否存在数据库中，如果不存在就创建2 3 4级分类
                        $name = Category::where('name',$v[6])->get();
                        if (!$name->first()){
                            $category_2 = new Category;
                            $name_2 = Category::where('name',$v[5])->get()->first()->name;
                            $name_2 = Category::where('name',$name_2)->get();

                            $category_2->parent_id = $name_2->first()->id;
                            $category_2->name = $v[6];
                            $category_2->brand_id = $name_2->first()->brand_id;
                            $category_2->save();       //--分类2
                            if ($v[7]){

                                $category_3 = new Category;
                                $name_3 = Category::where('name',$v[6])->get()->first()->name;
                                $name_3 = Category::where('name',$name_3)->get();

                                $category_3->parent_id = $name_3->first()->id;
                                $category_3->name = $v[7];
                                $category_3->brand_id = $name_3->first()->brand_id;
                                $category_3->save();       //--分类3
                                if ($v[8]){

                                    $category_4 = new Category;
                                    $name_4 = Category::where('name',$v[7])->get()->first()->name;
                                    $name_4 = Category::where('name',$name_4)->get();

                                    $category_4->parent_id = $name_4->first()->id;
                                    $category_4->name = $v[8];
                                    $category_4->brand_id = $name_4->first()->brand_id;
                                    $category_4->save();       //--分类4

                                }
                            }
                        }else{ //如果2级分类也存在
                            //再判读三级分类是否存在，如果不存在就创建3 4级分类
                            if($v[7]){
                                $name = Category::where('name',$v[7])->get();
                                if (!$name->first()){
                                    $category_3 = new Category;
                                    $name_3 = Category::where('name',$v[6])->get()->first()->name;
                                    $name_3 = Category::where('name',$name_3)->get();

                                    $category_3->parent_id = $name_3->first()->id;
                                    $category_3->name = $v[7];
                                    $category_3->brand_id = $name_3->first()->brand_id;
                                    $category_3->save();       //--分类3
                                    if ($v[8]){

                                        $category_4 = new Category;
                                        $name_4 = Category::where('name',$v[7])->get()->first()->name;
                                        $name_4 = Category::where('name',$name_4)->get();

                                        $category_4->parent_id = $name_4->first()->id;
                                        $category_4->name = $v[8];
                                        $category_4->brand_id = $name_4->first()->brand_id;
                                        $category_4->save();       //--分类4

                                    }
                                }else{
                                    //如果三级分类也存在在数据库中，判断四级分类
                                    if($v[8]){
                                        $name = Category::where('name',$v[8])->get();
                                        if (!$name->first()){
                                            $category_4 = new Category;
                                            $name_4 = Category::where('name',$v[7])->get()->first()->name;
                                            $name_4 = Category::where('name',$name_4)->get();

                                            $category_4->parent_id = $name_4->first()->id;
                                            $category_4->name = $v[8];
                                            $category_4->brand_id = $name_4->first()->brand_id;
                                            $category_4->save();       //--分类4
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }
        }
        //products表操作
        foreach ($res as $k=>$v){
            $products[$k]['model'] = $v[0];
            $products[$k]['special_price'] = $v[4];
            $products[$k]['price'] = $v[3];
            $products[$k]['image'] = $v[2];

            //判断当前分类  默认存在一级分类
            $category_id = Category_description::where('name',$v[5])->first();

            if(!$category_id){
                return redirect()->back()->with('cateIsNull',1);
            }

            $products[$k]['category_id'] = $category_id->category_id;
            $products[$k]['created_at'] = date('Y-m-d H:i:s', time());
            $products[$k]['updated_at'] = date('Y-m-d H:i:s', time());
            //验证
            $validator = Validator::make($products[$k], [
                'model'=>'unique:products|required',
                'special_price'=>'nullable',
                'price'=>'required',
                'image'=>'nullable|string',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

        }

        DB::table('products')->insert($products);

        //product_descriptions表操作
        foreach ($res as $k=>$v){
                    $product_descriptions[$k]['product_model'] = $v[0];
                    $product_descriptions[$k]['language_id'] = $request->improt_language_id;
                    $product_descriptions[$k]['brand_id'] = $request->improt_language_id;
                    $product_descriptions[$k]['product_name'] = $v[1];
                    $product_descriptions[$k]['product_description'] = $v[6];
                    $product_descriptions[$k]['created_at'] = date('Y-m-d H:i:s', time());
                    $product_descriptions[$k]['updated_at'] = date('Y-m-d H:i:s', time());

            //验证
            $validator = Validator::make($product_descriptions[$k], [
                'product_model'=>'required',
                'language_id'=>'required',
                'product_name'=>'required',
                'product_description'=>'nullable',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }
        DB::table('product_descriptions')->insert($product_descriptions);


        return redirect()->back()->with('success','上传成功');


}

}
