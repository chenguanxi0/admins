<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Web;
use App\Web_log;
use App\Web_product;
use Chumper\Zipper\Facades\Zipper;
use Illuminate\Http\Request;
use App\Language;
use App\Product_description;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;

class WebController extends Controller
{
    public function webList()
    {
        $webs = Web::query()->orderBy('created_at','desc')->get();
        $languages = Language::all();
        $brands = Brand::all();
        return view('webs/list',compact('webs','languages','brands'));
    }
    public function add(Request $request)
    {
        if ($request->method() == 'GET'){
            $languages = Language::all();
            $brands = Brand::all();
            return view('webs/add',compact('languages','brands'));
        }
        //验证
        $this->validate($request,[
            'url'=>'unique:webs|string|required',
            'type'=>'required',
            'brand_id'=>'integer|required',
            'language_id'=>'integer|required'
        ]);
        $web = new Web;
        $web->url = str_replace('www.','',$request->url);
        $web->type = $request->type;
        $web->brand_id = $request->brand_id;
        $web->language_id = $request->language_id;
        $web->save();

        return redirect('webs/list')->with('success',1);
    }
    public function webSet(Web $web,Request $request)
    {

        $brand = $web->brand_id;
        $language_id = $web->language_id;
        $categorys = Category::query()
            ->where('parent_id', 0)
            ->where('brand_id', $brand)
            ->where('language_id', $language_id)
            ->get();
        return view('webs/set', compact( 'web', 'categorys'));
    }
    public function categoryList(Web $web,Request $request,Category $category)
    {
        $brand = $web->brand_id;
        $language_id = $web->language_id;
        $cateArrs = array();
        $cateArrs[0] = array($request->category_1,Category::query()->find($request->category_1)->name);
        $products = Product_description::query()
            ->where('category_1',$request->category_1)->get();
        if($request->category_2){
            $products = $products->where('category_2',$request->category_2);
            $cateArrs[1] = array($request->category_2,Category::query()->find($request->category_2)->name);
        }
        if($request->category_3){
            $products = $products->where('category_3',$request->category_3);
            $cateArrs[2] = array($request->category_3,Category::query()->find($request->category_3)->name);
        }
        if($request->category_4){
            $products = $products->where('category_4',$request->category_4);
            $cateArrs[3] = array($request->category_4,Category::query()->find($request->category_4)->name);
        }

        $firstCategorys = Category::query()
            ->where('parent_id', 0)
            ->where('brand_id', $brand)
            ->where('language_id', $language_id)
            ->get();
        $secondCategorys = Category::query()
            ->where('parent_id', $cateArrs[0][0])
            ->where('language_id', $language_id)
            ->get();
        $web_products = Web_product::query()
            ->where('web_id',$web->id)
            ->get();
        foreach ($web_products as $k=>$v){

            $web_products[$k]->name = $v->getProducts()->where('language_id',$web->language_id)->first()->product_name;

        }
        return view('webs/categoryList', compact('products', 'web', 'firstCategorys','secondCategorys','web_products','cateArrs'))->with('noLink',0);

    }
    public function addSelect(Request $request)
    {

        $web_id = $request->web_id;
        $models = $request->models;
        $language_id = $request->language_id;
        $modelArr = explode(',',substr($models, 0, -1));
        foreach ($modelArr as $k=>$v){
            $productArr[$k]['web_id'] = $web_id;
            $productArr[$k]['model'] =explode('|',$v)[0];
            $productArr[$k]['language_id'] = $language_id;
            $productArr[$k]['path'] =explode('|',$v)[1];
            $productArr[$k]['created_at'] = date('Y-m-d H:i:s', time());
            $productArr[$k]['updated_at'] = date('Y-m-d H:i:s', time());

            $results[$k]['model'] = explode('|',$v)[0];
            $results[$k]['path'] = explode('|',$v)[1];
            $results[$k]['name'] = explode('|',$v)[2];
            $results[$k]['image'] = explode('|',$v)[3];
            $results[$k]['language'] = explode('|',$v)[4];
        }
        foreach ($productArr as $k=>$v) {
            $res = Web_product::query()
                ->firstOrCreate(['web_id'=>$request->web_id,'model' =>$v['model'],'language_id'=>$v['language_id'],'path'=>$v['path']],$v);
        }

//        $res = DB::table('web_products')->insert($productArr);
        if ($res){
            return $results;
        }else{
            return false;
        }
    }
    public function delSelect(Request $request)
    {
        $result = Web_product::query()->where('model',$request->model)->where('language_id',$request->language_id)->delete();
        if (!$result){
            return false;
        }
        return 1;

    }
    //导出产品
    public function export(Web $web,Request $request)
    {

        $web_products = Web_product::query()
            ->where('web_id',$web->id)
            ->get();
        $cellData = array();
        $type = $web->type;

        if ($type == 'zc'){
            $AdditionalImages = array();
            foreach ($web_products as $k=>$web_product){
                $product_description = $web_product->getProducts()
                    ->where('product_model',$web_product->model)
                    ->where('language_id',$web_product->language_id)
                    ->where('path',$web_product->path)->first();
                $cellData[$k]['v_products_model'] = $web_product->model;
                $cellData[$k]['v_products_image'] = $web_product->getProduct->image;
                $cellData[$k]['v_products_name_1'] = $product_description->product_name;
                $cellData[$k]['v_products_description_1'] = $product_description->product_description;
                $cellData[$k]['v_products_url_1'] = '';
                $cellData[$k]['v_specials_price'] =  $web_product->getProduct->special_price;
                $cellData[$k]['v_specials_last_modified'] = '';
                $cellData[$k]['v_specials_expires_date'] = '';
                $cellData[$k]['v_products_price'] =  $web_product->getProduct->price;
                $cellData[$k]['v_products_weight'] = '0.5';
                $cellData[$k]['v_last_modified'] = '';
                $cellData[$k]['v_date_added'] = '';
                $cellData[$k]['v_products_quantity'] = '1000';
                $cellData[$k]['v_manufacturers_name'] = '';
                $cellData[$k]['v_categories_name_1'] = $product_description->getCate1->name;
                $cellData[$k]['v_categories_name_2'] = $product_description->category_2 == null ? '' : $product_description->getCate2->name;
                $cellData[$k]['v_categories_name_3'] = $product_description->category_3 == null ? '' : $product_description->getCate3->name;
                $cellData[$k]['v_categories_name_4'] = $product_description->category_4== null ? '' : $product_description->getCate4->name;
                $cellData[$k]['v_categories_name_5'] = '';
                $cellData[$k]['v_categories_name_6'] = '';
                $cellData[$k]['v_categories_name_7'] = '';
                $cellData[$k]['v_tax_class_title'] = '';
                $cellData[$k]['v_status'] = '1';
                $cellData[$k]['v_metatags_products_name_status'] = '0';
                $cellData[$k]['v_metatags_title_status'] = '0';
                $cellData[$k]['v_metatags_model_status'] = '0';
                $cellData[$k]['v_metatags_price_status'] = '0';
                $cellData[$k]['v_metatags_title_tagline_status'] = '0';
                $cellData[$k]['v_metatags_title_1'] = '';
                $cellData[$k]['v_metatags_keywords_1'] = '';
                $cellData[$k]['v_metatags_description_1'] = '';
                array_push($AdditionalImages,str_replace('\\', '/', public_path()).'/storage/'.$web_product->getProduct->image);
                $images = explode('|',$web_product->getProduct->addImages);

                foreach ($images as $image){

                    if(file_exists(str_replace('\\', '/', public_path()).'/storage/'.$image)){
                        array_push($AdditionalImages,str_replace('\\', '/', public_path()).'/storage/'.$image);
                    }
                }
            }
            array_unshift($cellData,['v_products_model',
                'v_products_image',
                'v_products_name_1',
                'v_products_description_1',
                'v_products_url_1',
                'v_specials_price',
                'v_specials_last_modified',
                'v_specials_expires_date',
                'v_products_price',
                'v_products_weight',
                'v_last_modified',
                'v_date_added',
                'v_products_quantity',
                'v_manufacturers_name',
                'v_categories_name_1',
                'v_categories_name_2',
                'v_categories_name_3',
                'v_categories_name_4',
                'v_categories_name_5',
                'v_categories_name_6',
                'v_categories_name_7',
                'v_tax_class_title',
                'v_status',
                'v_metatags_products_name_status',
                'v_metatags_title_status',
                'v_metatags_model_status',
                'v_metatags_price_status',
                'v_metatags_title_tagline_status',
                'v_metatags_title_1',
                'v_metatags_keywords_1',
                'v_metatags_description_1'
            ]);


            //执行导出操作
            if ($request->active == 1){
                $web->update(['hasSet'=>1]);
                $res = Excel::create('zc_'.$web->url,function($excel) use ($cellData){
                    $excel->sheet('score', function($sheet) use ($cellData){
                        $sheet->rows($cellData);
                    });
                })->export('xls');

            }else{
                $namepath = 'storage/zip/zc_'.time().'.zip';
                Zipper::make($namepath)->add($AdditionalImages)->close();
                $web->update(['hasSet'=>1,'zip'=>$namepath]);
                $webs = Web::query()->orderBy('created_at','desc')->get();
                $languages = Language::all();
                $brands = Brand::all();
                return view('webs/list',compact('webs','languages','brands','namepath'));
            }

        }

        if ($type == 'op'){

            if ($request->active == 1){
                $arr = array();
                foreach ($web_products as $k=>$web_product) {
                    $paths[$k] = explode('-',$web_product->path);
                }
                foreach ($paths as $kk=>$path){
                    foreach($path as $kkk=>$v){
                        array_push($arr,$v);
                    }
                }
                $idArrs = array_values(array_unique($arr));

                //Categories
                foreach ($idArrs as $k=>$idArr){
                    $categorys = Category::query()->find($idArr);
                    $categories[$k]['category_id'] = $idArr;
                    $categories[$k]['parent_id'] = $categorys->parent_id;
                    $categories[$k]['filters'] = '';
                    $categories[$k]['name'] = $categorys->name;
                    $categories[$k]['top'] = 'true';
                    $categories[$k]['columns'] = '1';
                    $categories[$k]['sort_order'] = '';
                    $categories[$k]['image_name'] = '';
                    $categories[$k]['date_added'] = '';
                    $categories[$k]['date_modified'] = '';
                    $categories[$k]['language_id'] = $categorys->language_id;
                    $categories[$k]['seo_keyword'] = $categorys->name;
                    $categories[$k]['description'] = '';
                    $categories[$k]['meta_title'] = $categorys->name;
                    $categories[$k]['meta_description'] = '';
                    $categories[$k]['meta_keywords'] = '';
                    $categories[$k]['store_ids'] = 0;
                    $categories[$k]['layout'] = '0:';
                    $categories[$k]['status enabled'] = 'true';
                }
                array_unshift($categories,[
                    'category_id',
                    'parent_id',
                    'filters',
                    'name',
                    'top',
                    'columns',
                    'sort_order',
                    'image_name',
                    'date_added',
                    'date_modified',
                    'language_id',
                    'seo_keyword',
                    'description',
                    'meta_title',
                    'meta_description',
                    'meta_keywords',
                    'store_ids',
                    'layout',
                    'status enabled',
                ]);

                //FilterGroup
                $FilterGroup = array(array(0=>'filter_group_id',1=>'language_id',2=>'name',3=>'sort_order'));

                //Filter
                $Filter = array(array(0=>'filter_id',1=>'filter_group_id',2=>'language_id',3=>'name',4=>'sort_order'));

                //Products Descriptions  Specials
                foreach ($web_products as $k=>$web_product) {
                    $product_description = $web_product->getProducts()
                        ->where('product_model', $web_product->model)
                        ->where('language_id', $web_product->language_id)
                        ->where('path', $web_product->path)->first();
                    $p = $web_product->getProduct;
                    $products[$k]['product_id'] = $k + 1;
                    $products[$k]['categories'] = str_replace('-',',',$product_description->path);
                    $products[$k]['filters'] = '';
                    $products[$k]['sku'] = '';
                    $products[$k]['upc'] = '';
                    $products[$k]['ean'] = '';
                    $products[$k]['jan'] = '';
                    $products[$k]['isbn'] = '';
                    $products[$k]['mpn'] = '';
                    $products[$k]['location'] = '';
                    $products[$k]['quantity'] = '1000';
                    $products[$k]['model'] = $web_product->model;
                    $products[$k]['manufacturer'] = '';
                    $products[$k]['image_name'] = $p->image;
                    $products[$k]['requires shipping'] = 'yes';
                    $products[$k]['price'] = $p->price;
                    $products[$k]['points'] = '';
                    $products[$k]['date_added'] = '2009-02-03 16:06:50';
                    $products[$k]['date_modified'] = '2019-05-20 10:42:16';
                    $products[$k]['date_available'] = '2009-02-03';
                    $products[$k]['weight'] = '';
                    $products[$k]['unit'] = 'g';
                    $products[$k]['length'] = '0';
                    $products[$k]['width'] = '0';
                    $products[$k]['height'] = '0';
                    $products[$k]['length unit'] = 'cm';
                    $products[$k]['status enabled'] = 'true';
                    $products[$k]['tax_class_id'] = '';
                    $products[$k]['viewed'] = '';
                    $products[$k]['seo_keyword'] = $web_product->model;
                    $products[$k]['stock_status_id'] = 7;
                    $products[$k]['store_ids'] = 0;
                    $products[$k]['layout'] = '0:';
                    $products[$k]['related_ids'] = '1,2,3,4,5';
                    $products[$k]['sort_order'] = '0';
                    $products[$k]['subtract'] = 'true';
                    $products[$k]['minimum'] = '1';

                    $Descriptions[$k]['product_id'] = $k + 1;
                    $Descriptions[$k]['name'] = $product_description->product_name;
                    $Descriptions[$k]['language_id'] = $web_product->language_id;
                    $Descriptions[$k]['description'] = $product_description->product_description;
                    $Descriptions[$k]['meta_title'] = $product_description->product_name;
                    $Descriptions[$k]['meta_description'] = '';
                    $Descriptions[$k]['meta_keywords'] = '';
                    $Descriptions[$k]['tags'] = '';

                    $Specials[$k]['product_id'] = $k + 1;
                    $Specials[$k]['customer_group_id'] = 1;
                    $Specials[$k]['priority'] = 1;
                    $Specials[$k]['price'] = $p->special_price;
                    $Specials[$k]['date_start'] = '0000-00-00';
                    $Specials[$k]['date_end'] = '0000-00-00';
                }
                array_unshift($products,[
                    'product_id',
                    'categories',
                    'filters',
                    'sku',
                    'upc',
                    'ean',
                    'jan',
                    'isbn',
                    'mpn',
                    'location',
                    'quantity',
                    'model',
                    'manufacturer',
                    'image_name',
                    'requires shipping',
                    'price',
                    'points',
                    'date_added',
                    'date_modified',
                    'date_available',
                    'weight',
                    'unit',
                    'length',
                    'width',
                    'height',
                    'length unit',
                    'status enabled',
                    'tax_class_id',
                    'viewed',
                    'seo_keyword',
                    'stock_status_id',
                    'store_ids',
                    'layout',
                    'related_ids',
                    'sort_order',
                    'subtract',
                    'minimum',
                ]);
                array_unshift($Descriptions,[
                    'product_id',
                    'name',
                    'language_id',
                    'description',
                    'meta_title',
                    'meta_description',
                    'meta_keywords',
                    'tags',
                ]);
                array_unshift($Specials,['product_id','customer_group_id','priority','price','date_start','date_end']);

                //AdditionalImages
                foreach ($web_products as $k=>$web_product){
                    $product = $web_product->getProduct;
                    $AdditionalImages_2[$k+1] = (explode('|',$product->addImages));
                    array_push($AdditionalImages_2[$k+1],$product->image);
                }
                $AdditionalImages = array();
                $filenames = array();
                foreach ($AdditionalImages_2 as $k=>$v){
                    foreach ($v as $kk=>$vv){
                        array_push($AdditionalImages,['product_id'=>$k,'image'=>$vv,'sort_order'=>0]);
                        if(file_exists(str_replace('\\', '/', public_path()).'/storage/'.$vv)){
                            array_push($filenames,str_replace('\\', '/', public_path()).'/storage/'.$vv);
                        }
                    }
                }
                array_unshift($AdditionalImages,[
                    'product_id',
                    'image',
                    'sort_order'
                ]);

                //ProductOptions
                $ProductOptions = array(array('product_id','option_id','option_value','option_value_id','required','quantity','subtract','price','price prefix','points','points prefix','weight','weight prefix'));

                //Options
                $Options = array(array('option_id','language_id','option_name','type','sort_order'));

                //OptionValues
                $OptionValues = array(array('option_value_id','option_id','language_id','value','image','sort_order'));

                //Attributes
                $Attributes = array(array('product_id','language_id','attribute_group','attribute_name','text'));

                //CustomerGroups
                $CustomerGroups = array(array('customer_group_id','language_id','name','description'));

                //Discounts
                $Discounts = array(array('product_id','customer_group_id','quantity','priority','price','date_start','date_end'));

                //Rewards
                $Rewards = array(array('product_id','customer_group_id','points'));


                $web->update(['hasSet'=>1]);
                $res = Excel::create('op_'.$web->url,function($excel) use ($categories,$FilterGroup,$products,$Descriptions,$AdditionalImages,$ProductOptions,$Options,$OptionValues,$Attributes,$CustomerGroups,$Specials,$Discounts,$Rewards){
                    $excel->sheet('Categories', function($sheet) use ($categories){
                        $sheet->rows($categories);
                    });
                    $excel->sheet('FilterGroup', function($sheet) use ($FilterGroup){
                        $sheet->rows($FilterGroup);
                    });
                    $excel->sheet('Products', function($sheet) use ($products){
                        $sheet->rows($products);
                    });
                    $excel->sheet('Descriptions', function($sheet) use ($Descriptions){
                        $sheet->rows($Descriptions);
                    });
                    $excel->sheet('AdditionalImages', function($sheet) use ($AdditionalImages){
                        $sheet->rows($AdditionalImages);
                    });
                    $excel->sheet('ProductOptions', function($sheet) use ($ProductOptions){
                        $sheet->rows($ProductOptions);
                    });
                    $excel->sheet('Options', function($sheet) use ($Options){
                        $sheet->rows($Options);
                    });
                    $excel->sheet('OptionValues', function($sheet) use ($OptionValues){
                        $sheet->rows($OptionValues);
                    });
                    $excel->sheet('Attributes', function($sheet) use ($Attributes){
                        $sheet->rows($Attributes);
                    });
                    $excel->sheet('CustomerGroups', function($sheet) use ($CustomerGroups){
                        $sheet->rows($CustomerGroups);
                    });
                    $excel->sheet('Specials', function($sheet) use ($Specials){
                        $sheet->rows($Specials);
                    });
                    $excel->sheet('Discounts', function($sheet) use ($Discounts){
                        $sheet->rows($Discounts);
                    });
                    $excel->sheet('Rewards', function($sheet) use ($Rewards){
                        $sheet->rows($Rewards);
                    });
                })->export('xls');
            }else{

                //AdditionalImages
                foreach ($web_products as $k=>$web_product){
                    $product = $web_product->getProduct;
                    $AdditionalImages_2[$k+1] = (explode('|',$product->addImages));
                    array_push($AdditionalImages_2[$k+1],$product->image);
                }
                $AdditionalImages = array();
                $filenames = array();
                foreach ($AdditionalImages_2 as $k=>$v){
                    foreach ($v as $kk=>$vv){
                        array_push($AdditionalImages,['product_id'=>$k,'image'=>$vv,'sort_order'=>0]);
                        if(file_exists(str_replace('\\', '/', public_path()).'/storage/'.$vv)){
                            array_push($filenames,str_replace('\\', '/', public_path()).'/storage/'.$vv);
                        }
                    }
                }

                $namepath = 'storage/zip/op_'.time().'.zip';
                Zipper::make($namepath)->add($filenames)->close();
                $web->update(['hasSet'=>1,'zip'=>$namepath]);
                $webs = Web::query()->orderBy('created_at','desc')->get();
                $languages = Language::all();
                $brands = Brand::all();
                return view('webs/list',compact('webs','languages','brands','namepath'));

            }

        }

        if ($type == 'ot'){
            //产品信息
            $AdditionalImages = array();

            foreach ($web_products as $k=>$web_product){
                $otProduct_description = $web_product->getProducts->where('language_id',$web_product->language_id)->first();
                $otProduct = $otProduct_description->isProduct;
                $prefix = explode('/',$otProduct->image)[0];
                $image = explode('/',$otProduct->image)[1];

                $otProducts[$k]['Seo标题'] = '';
                $otProducts[$k]['Seo关键字'] = '';
                $otProducts[$k]['Seo描述'] = '';
                $otProducts[$k]['一级分类'] = $otProduct_description->getCate1->name;
                $otProducts[$k]['二级分类'] = $otProduct_description->getCate2 == null ? null : $otProduct_description->getCate2->name;
                $otProducts[$k]['三级分类'] = $otProduct_description->getCate3 == null ? null : $otProduct_description->getCate3->name;
                $otProducts[$k]['四级分类'] = $otProduct_description->getCate4 == null ? null : $otProduct_description->getCate4->name;
                $otProducts[$k]['产品ID'] = $k+1;
                $otProducts[$k]['产品名字'] = $otProduct_description->product_name;
                $otProducts[$k]['产品原始价格'] = $otProduct->price;
                $otProducts[$k]['产品折扣'] = $otProduct->special_price/$otProduct->price;
                $otProducts[$k]['产品售价'] = $otProduct->special_price;
                $otProducts[$k]['产品型号'] = $otProduct_description->product_model;
                $otProducts[$k]['产品Sku'] = $otProduct_description->product_model;
                $otProducts[$k]['产品自定义属性'] = 0;
                $otProducts[$k]['产品状态'] = 1;
                $otProducts[$k]['产品排序'] = 9999;
                $otProducts[$k]['产品描述'] = $otProduct_description->product_description;
                $otProducts[$k]['图片文件夹'] = $prefix;
                $otProducts[$k]['主图'] = $image;
                $otProducts[$k]['详细图'] = $image.'|'.str_replace($prefix.'/','',$otProduct->addImages);

                array_push($AdditionalImages,str_replace('\\', '/', public_path()).'/storage/'.$web_product->getProduct->image);
                $images = explode('|', $otProduct->addImages);

                foreach ($images as $v){
                    if(file_exists(str_replace('\\', '/', public_path()).'/storage/'.$v)){
                        array_push($AdditionalImages,str_replace('\\', '/', public_path()).'/storage/'.$v);
                    }
                }

            }
            array_unshift($otProducts,[
                'Seo标题',
                'Seo关键字',
                'Seo描述',
                '一级分类',
                '二级分类',
                '三级分类',
                '四级分类',
                '产品ID',
                '产品名字',
                '产品原始价格',
                '产品折扣',
                '产品售价',
                '产品型号',
                '产品Sku',
                '产品自定义属性',
                '产品状态',
                '产品排序',
                '产品描述',
                '图片文件夹',
                '主图',
                '详细图',
            ]);

            //产品属性
            $Options = array(array(
                '产品ID',
                '选项名称1',
                '选项类型1',
                '是否必选1',
                '选项值1',
                '选项名称2',
                '选项类型2',
                '是否必选2',
                '选项值2',
                '选项名称3',
                '选项类型3',
                '是否必选3',
                '选项值3',
                '选项名称4',
                '选项类型4',
                '是否必选4',
                '选项值4',
                '选项名称5',
                '选项类型5',
                '是否必选5',
                '选项值5',
                '选项名称6',
                '选项类型6',
                '是否必选6',
                '选项值6',
                '选项名称7',
                '选项类型7',
                '是否必选7',
                '选项值7',
                '选项名称8',
                '选项类型8',
                '是否必选8',
                '选项值8',
            ));

            //产品扩展
            $expands = array(array(
                '产品ID',
                '产品key1',
                '产品value1',
                '产品key2',
                '产品value2',
                '产品key3',
                '产品value3',
                '产品key4',
                '产品value4',
                '产品key5',
                '产品value5',
                '产品key6',
                '产品value6',
                '产品key7',
                '产品value7',
                '产品key8',
                '产品value8',
            ));
            if ($request->active == 1){
                $web->update(['hasSet'=>1]);
                $res = Excel::create('ot_'.$web->url,function($excel) use ($otProducts,$Options,$expands){
                    $excel->sheet('产品信息', function($sheet) use ($otProducts){
                        $sheet->rows($otProducts);
                    });
                    $excel->sheet('产品属性', function($sheet) use ($Options){
                        $sheet->rows($Options);
                    });
                    $excel->sheet('产品扩展', function($sheet) use ($expands){
                        $sheet->rows($expands);
                    });
                })->export('xls');
            }else{
                $namepath = 'storage/zip/ot_'.time().'.zip';
                Zipper::make($namepath)->add($AdditionalImages)->close();
                $web->update(['hasSet'=>1,'zip'=>$namepath]);
                $webs = Web::query()->orderBy('created_at','desc')->get();
                $languages = Language::all();
                $brands = Brand::all();
                return view('webs/list',compact('webs','languages','brands'));
            }


        }

    }
    public function delete(Web $web)
    {

        $web->delete();
        Web_product::query()->where('web_id',$web->id)->delete();
        return redirect()->back()->with('delete',1);
    }
    public function store(Request $request)
    {
        $web = Web::query()->find($request->web_id);
        $web->url = $request->url;
        $web->type = $request->type;
        $web->brand_id = $request->brand_id;
        $web->language_id = $request->language_id;
        $web->save();
        return redirect()->route('webList')->with('stroe',1);
    }
    public function getWeb(Request $request)
    {
        $web = Web::query()->find($request->web_id);
        return $web;
    }
    public function getLog(Request $request)
    {
        $web_logs = Web_log::query()->where('web_id',$request->web_id)->get();
        return $web_logs;
    }
}
