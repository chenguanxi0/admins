<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\MyClass\timeDeal;
use App\Commit;
use App\Product;
use App\Product_description;
use App\Product_log;
use App\UploadLog;
use App\Web;
use App\Web_log;
use App\Web_product;
use Illuminate\Http\Request;
use App\Language;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;



class ProductController extends Controller
{
    //品牌选择
    public function brand()
    {
        return view('products/brand');
    }
    public function batch(Request $request)
    {
        $modelArrs = $request->modelArrs;
        $language_id = $request->language_id;
        $active = $request->active;
        $radio_status = $request->radio_status;
        $commitsNum = $request->commitsNum;
        $days = $request->days;

        $mult = $request->mult;
        $models = explode(',',rtrim($modelArrs,','));

        if($commitsNum != null){
                 Product::query()
                ->whereIn('model',$models)
                ->update(['commitsNum' => $commitsNum]);
        }
        if($days != null){
            Product::query()
                ->whereIn('model',$models)
                ->update(['days' => $days]);
            $a = new timeDeal();
            $a->allDeal();

        }

        if ($active == 0){ //修改属性
            if ($radio_status != null){

                switch ($radio_status){
                    case 0;
                        $statuName = '普通产品';
                        break;
                    case 1;
                        $statuName = '广告图产品';
                        break;
                    case 2;
                        $statuName = '特价产品';
                        break;
                    case 3;
                        $statuName = '最新产品';
                        break;
                    case 4;
                        $statuName = '最热产品';
                        break;
                }
                foreach ($models as $k=>$model){
                    if ($model != '' || $model != null){
                        $product_descriptions[$k]['product_model'] = $model;
                        $product_descriptions[$k]['language_id'] = $language_id;
                        $product_descriptions[$k]['status'] = $radio_status;

                        $product_logs[$k]['model'] = $model;
                        $product_logs[$k]['active'] = "产品属性修改为<span class='text-danger'>".$statuName."</span>";
                        $product_logs[$k]['created_at'] = date('Y-m-d H:i:s', time());

                    }
                }
                $this->updateBatch_2('product_descriptions',$product_descriptions);

                //写入product日志
                $this->savePlog($product_logs);
            }



        }else{ //修改价格
            foreach ($models as $k=>$model){
                if ($model != '' || $model != null) {
                    $special = Product::where('model', $model)->first()->special_price;
                    $products[$k]['model'] = $model;
                    $products[$k]['special_price'] = $mult * $special;
                    $products[$k]['sureChange'] = 0;
                    $products[$k]['priceChange'] = $mult;

                    $product_logs[$k]['model'] = $model;
                    $product_logs[$k]['active'] = "产品价格调整了<span class='text-danger'>".$mult."</span>倍,调整前为<span class='text-danger'>".$special."</span>";
                    $product_logs[$k]['created_at'] = date('Y-m-d H:i:s', time());
                }
            }
            $this->updateBatch('products',$products);
            $this->savePlog($product_logs);
        }
        return back();

    }
    //同步价格接口
    public function priceChange($url,Request $request)
    {

        $ch = curl_init();
        $urll = $url.'/priceChange.php?active=1&mult='.$request->mult;
//设置选项，包括URL
//        dd($urll);
        curl_setopt($ch, CURLOPT_URL, $urll);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
//执行并获取HTML文档内容
        $output = curl_exec($ch);
//释放curl句柄
        curl_close($ch);
        $commits = json_decode($output);
        if($commits->status != 1){
            return back()->with('false',1);
        };

        DB::table('webs')->where('url',$url)->update(['priceChange'=>$request->mult]);

        $web_id = Web::query()->where('url',$url)->first()->id;
        $active = '网站整体价格调整'."<span class='text-danger'>".$request->mult."</span>".'倍';
        $this->saveLog($web_id,$active);
        return back()->with('priceChange',1);
    }
    public function productPriceSure(Request $request)
    {
       //遍历所有包含此model的网站，再分别给每个网站发送请求
        $model = $request->model;
        $special_price = $request->special_price;
        $web_products = Web_product::query()->where('model',$model)->get();

        foreach ($web_products as $k=>$web_product){
            $mult = $web_product->getWeb->priceChange;
            $url = $web_product->getWeb->url;
            $this->modelPriceSure($url,$model,$mult,$special_price);
        }
        Product::query()->where('model',$model)->update(['sureChange'=>1]);
        return back()->with('priceChange',1);
    }
    public function modelPriceSure($url,$model,$mult,$special_price)
    {
        //修改单个产品价格
        $ch = curl_init();
        $urll = $url.'/priceChange.php?active=2&mult='.$mult.'&model='.$model.'&price='.$special_price;
//设置选项，包括URL
//        dd($urll);
        curl_setopt($ch, CURLOPT_URL, $urll);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
//执行并获取HTML文档内容
        $output = curl_exec($ch);
//释放curl句柄
        curl_close($ch);
        $res = json_decode($output);

        return $res;
    }
    public function saveLog($web_id,$active)
    {
        $web_log = new Web_log();
        $web_log->web_id = $web_id;
        $web_log->active = $active;
        $web_log->save();
    }
    public function savePlog($arr)
    {
        DB::table('product_logs')->insert($arr);
    }
    //产品列表
    public function productsList(Request $request)
    {
        if ($request->method() == 'GET'){
            $languages = Language::all();
            return view('products/list',compact('languages'))->with('noLink',1);
        }

    }

    public function categoryList(Category $category,Request $request)
    {
        $path = $category->path;
        $brand_id = $category->brand_id;
        $status = $request->status;
//        $products = Product_description::query()->where('path',$path)->get();
        $lanArrs = array($category->getLan->id,$category->getLan->code);
        $languages = Language::all();
        $cateArrss = explode('-',$path);
        $products = Product_description::query()
            ->where('category_1',$cateArrss[0])
            ->where('status',$status)
            ->get();
        if(isset($cateArrss[1])){
            $products = $products->where('category_2',$cateArrss[1]);
        }
        if(isset($cateArrss[2])){
            $products = $products->where('category_3',$cateArrss[2]);
        }
        if(isset($cateArrss[3])){
            $products = $products->where('category_4',$cateArrss[3]);
        }

        for ($i=0;$i<count($cateArrss);$i++){
            $cateArrs[$i][0]= $cateArrss[$i];
            $cateArrs[$i][1]= Category::query()->find($cateArrss[$i])->name;
        }
        $firstCategorys = Category::query()
            ->where('parent_id', 0)
            ->where('brand_id',$brand_id)
            ->where('language_id', $category->getLan->id)
            ->get();
        $secondCategorys = Category::query()
            ->where('parent_id', $cateArrs[0][0])
            ->where('language_id', $category->getLan->id)
            ->get();
        return view('products/categoryList',compact('brand_id','status','firstCategorys','secondCategorys','products','languages','lanArrs','$cateArrs','category'))->with('noLink',0)->with('cateArrs',$cateArrs);
    }
    public function categoryHotList(Category $category,$status)
    {


        $path = $category->path;
        $brand_id = $category->brand_id;
//        $products = Product_description::query()->where('path',$path)->get();
        $lanArrs = array($category->getLan->id,$category->getLan->code);
        $languages = Language::all();
        $cateArrss = explode('-',$path);
        if ($status == 6){
            $products = Product_description::query()
                ->where('category_1',$cateArrss[0])
                ->get();
        }else{
            $products = Product_description::query()
                ->where('category_1',$cateArrss[0])
                ->where('status',$status)
                ->get();
        }
        if(isset($cateArrss[1])){
            $products = $products->where('category_2',$cateArrss[1]);
        }
        if(isset($cateArrss[2])){
            $products = $products->where('category_3',$cateArrss[2]);
        }
        if(isset($cateArrss[3])){
            $products = $products->where('category_4',$cateArrss[3]);
        }
        foreach ($products as $product){

            $product->noImg = $product->getCommits
                ->where('language_id',$category->language_id)->where('img',null)->count();
            //总数
            $product->havImg = $product->getCommits
                ->where('language_id',$category->language_id)->where('img','!=',null)->count();

        }
        for ($i=0;$i<count($cateArrss);$i++){
            $cateArrs[$i][0]= $cateArrss[$i];
            $cateArrs[$i][1]= Category::query()->find($cateArrss[$i])->name;
        }
        $firstCategorys = Category::query()
            ->where('parent_id', 0)
            ->where('brand_id',$brand_id)
            ->where('language_id', $category->getLan->id)
            ->get();
        $secondCategorys = Category::query()
            ->where('parent_id', $cateArrs[0][0])
            ->where('language_id', $category->getLan->id)
            ->get();
        return view('products/categoryHostList',compact('brand_id','status','firstCategorys','secondCategorys','products','languages','lanArrs','$cateArrs','category'))->with('noLink',0)->with('cateArrs',$cateArrs);

    }

    public function ajaxRes(Request $request)
    {
        $cds = Category::query()
            ->where('language_id',$request->language_id)
            ->where('parent_id',0)
            ->where('brand_id', $request->brand_id)
            ->get();
        if($cds->first()){
            return response()->json($cds);
        }else{
            return response()->json(null);
        }
    }
    public function getCategory(Request $request)
    {

        $cds = Category::query()
            ->where('parent_id',$request->category_id)
            ->get();
        if($cds->first()){
            return response()->json($cds);
        }else{
            return response()->json(null);
        }

    }
    public function addSession(Request $request)
    {
       $str = $request->str;
       $modelLanguages = explode(',',$str);
       foreach ($modelLanguages as $k=>$v){
           $models[$k] = (explode('|',$v));
       }
        $language_id = $models[0][1];
       foreach ($models as $k=>$v){
           $model[$k] = $v[0];
       }

    }

    //产品详情
    public function detail($model,$language_id)
    {
        //产品的公共信息
        $product = Product_description::query()->where('product_model',$model)->first();

        //产品带语言的信息
//        $lanProduct = $product->product_description->where('language_id',$language_id)->first();

        return view('products/detail',compact('product'));
    }
    //创建产品
    public function store(Request $request)
    {

       if ($request->method() == 'GET'){
           return view('/products/store');
       }else{

       }
    }
    //删除产品
    public function delete($model,$language_id)
    {
        $result = Product_description::where('product_model',$model)->where('language_id',$language_id)->delete();
        $model_2 = Product_description::where('product_model',$model)->get();
        if (!$model_2->first()){
            Product::where('model',$model)->delete();
        }
        return redirect('products/list')->with('delete',true);
    }
    //修改产品
    public function update($model,$language_id,Request $request)
    {
        //验证
        $this->validate($request,[
//            'special_price'=>'nullable',
//            'price'=>'required',
            'product_name'=>'required'
        ]);
        if($request->special_price){
            $data_1['special_price'] = $request->special_price;
        }else{
            $data_1['special_price'] = round((($request->costPrice+$request->freight)/0.45)/6.5,2);
        }
        //product表
//        $data_1['special_price'] = $request->special_price;
        $data_1['costPrice'] = $request->costPrice;
        $data_1['freight'] = $request->freight;
        $data_1['sureChange'] = 0;

        $product_logs = new Product_log;
        $product_logs['model'] = $model;
        $product_logs['active'] = "产品价格由<span class='text-danger'>".$request->special_price."</span>调整为<span class='text-danger'>".$data_1['special_price']."</span>";
        $product_logs['created_at'] = date('Y-m-d H:i:s', time());
        $product_logs->save();

        $product = new Product;
        $data_2['is_usable'] = $request->is_usable;
        $data_2['status'] = $request->radio_status;
        $data_2['product_name'] = $request->product_name;
        $data_2['product_description'] = $request->product_description;
        $product_description = new Product_description;


        if ($product->where('model',$model)->update($data_1) && $product_description->where('product_model',$model)->where('language_id',$language_id)->update($data_2)){
            return redirect()->back()->with('update',true);
        }else{
            return redirect()->back()->with('updateFail',true);
        }

        //product_description表
    }
    //搜索产品
    public function search(Request $request)
    {
        //根据名字和model来搜索产品
        $language_id = $request->lan;
        $modelArr = explode(',',$request->model);
        $products = Product_description::query()->whereIn('product_model',$modelArr)->where('language_id',$language_id)->get();
        foreach ($products as $product){

            $product->noImg = $product->getCommits->where('language_id',$language_id)->where('img',null)->count();
            //总数
            $product->havImg = $product->getCommits->where('language_id',$language_id)->where('img','!=',null)->count();

        }
        return view('search',compact('products'));
    }
    //上传产品
    public function import(Request $request){

        if ($request->method() == 'GET'){
            $languages = Language::all();
            $brands = Brand::all();
            return view('/products/import',compact('languages','brands'));
        }

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
                $name = Category::query()
                    ->where('compareName',trim(strtolower($v[5])))
                    ->where('language_id',$request->language_id)->get();
                if (!$name->first()){  //该name不存在,插入此条完整数据
                    $category = new Category;
                    $category->parent_id = 0;
                    $category->brand_id = $request->brand_id;
                    $category->language_id = $request->language_id;
                    $category->name = $v[5];
                    $category->compareName = trim(strtolower($v[5]));
                    $category->class = 1;
                    $category->path = DB::table('categorys')->max('id')+1;
                    $category->save();       //--分类1

                    if ($v[6]){
                        $category_2 = new Category;
                        $category_2->parent_id = $category->id;
                        $category_2->brand_id = $request->brand_id;
                        $category_2->language_id = $request->language_id;
                        $category_2->name = $v[6];
                        $category_2->compareName = trim(strtolower($v[6]));
                        $category_2->class = 2;
                        $category_2->path =  $category->path.'-'.(DB::table('categorys')->max('id')+1);
                        $category_2->save();       //--分类2
                        if ($v[7]){

                            $category_3 = new Category;
                            $category_3->parent_id = $category_2->id;
                            $category_3->brand_id = $request->brand_id;
                            $category_3->language_id = $request->language_id;
                            $category_3->name = $v[7];
                            $category_3->compareName = trim(strtolower($v[7]));
                            $category_3->class = 3;
                            $category_3->path =  $category_2->path.'-'.(DB::table('categorys')->max('id')+1);
                            $category_3->save();       //--分类3
                            if ($v[8]){

                                $category_4 = new Category;
                                $category_4->parent_id = $category_3->id;
                                $category_4->brand_id = $request->brand_id;
                                $category_4->language_id = $request->language_id;
                                $category_4->name = $v[8];
                                $category_4->compareName = trim(strtolower($v[8]));
                                $category_4->class = 4;
                                $category_4->path =  $category_3->path.'-'.(DB::table('categorys')->max('id')+1);
                                $category_4->save();         //--分类4
                            }
                        }
                    }



                }else{
                    // 2. 该分类已经存在 查找后面分类  --分类1

                    if($v[6]){ //判断上传的表当中是否有2级分类
                        // 先判读二级分类是否存在数据库中，如果不存在就创建2 3 4级分类
                        $name = Category::query()
                                 ->where('compareName',trim(strtolower($v[6])))
                                 ->where('language_id',$request->language_id)->get();
                        if (!$name->first()){ //不存在 创建234级   还没写二级分类下面的path逻辑
                            $category_2 = new Category;
                            $category_1 = Category::query()
                                ->where('compareName',trim(strtolower($v[5])))
                                ->where('language_id',$request->language_id)->get()->first();
                            $category_2->parent_id =$category_1->id;
                            $category_2->brand_id = $request->brand_id;
                            $category_2->language_id = $request->language_id;
                            $category_2->name = $v[6];
                            $category_2->compareName = trim(strtolower($v[6]));
                            $category_2->class = 2;
                            $category_2->path = $category_1->path.'-'.(DB::table('categorys')->max('id')+1);;
                            $category_2->save();       //--分类2
                            if ($v[7]){
                                $category_3 = new Category;
                                $category_3->parent_id = $category_2->id;
                                $category_3->brand_id = $request->brand_id;
                                $category_3->language_id = $request->language_id;
                                $category_3->name = $v[7];
                                $category_3->compareName = trim(strtolower($v[7]));
                                $category_3->class = 3;
                                $category_3->path =  $category_2->path.'-'.(DB::table('categorys')->max('id')+1);
                                $category_3->save();       //--分类3
                                if ($v[8]){
                                    $category_4 = new Category;
                                    $category_4->parent_id = $category_3->id;
                                    $category_4->brand_id = $request->brand_id;
                                    $category_4->language_id = $request->language_id;
                                    $category_4->name = $v[8];
                                    $category_4->compareName = trim(strtolower($v[8]));
                                    $category_4->class = 4;
                                    $category_4->path =  $category_3->path.'-'.(DB::table('categorys')->max('id')+1);
                                    $category_4->save();         //--分类4
                                }
                            }
                        }else{ //如果2级分类也存在,再判断path是否相同
                            $nowParent = Category::query()
                                ->where('compareName',trim(strtolower($v[5])))
                                ->where('language_id',$request->language_id)->get()->first();
                            $oldParent = $name->where('parent_id',$nowParent->id)->first();

                            //如果原数据的父级分类的path相同  说明是同一条数据 则继续判断三级分类

                            if (!$oldParent){ //不是同一条数据  则创建新分类
                                $category_2 = new Category;
                                $category_2->parent_id =$nowParent->id;
                                $category_2->brand_id = $request->brand_id;
                                $category_2->language_id = $request->language_id;
                                $category_2->name = $v[6];
                                $category_2->compareName = trim(strtolower($v[6]));
                                $category_2->class = 2;
                                $category_2->path = $nowParent->path.'-'.(DB::table('categorys')->max('id')+1);;
                                $category_2->save();       //--分类2
                                if ($v[7]){
                                    $category_3 = new Category;
                                    $category_3->parent_id = $category_2->id;
                                    $category_3->brand_id = $request->brand_id;
                                    $category_3->language_id = $request->language_id;
                                    $category_3->name = $v[7];
                                    $category_3->compareName = trim(strtolower($v[7]));
                                    $category_3->class = 3;
                                    $category_3->path =  $category_2->path.'-'.(DB::table('categorys')->max('id')+1);
                                    $category_3->save();       //--分类3
                                    if ($v[8]){
                                        $category_4 = new Category;
                                        $category_4->parent_id = $category_3->id;
                                        $category_4->brand_id = $request->brand_id;
                                        $category_4->language_id = $request->language_id;
                                        $category_4->name = $v[8];
                                        $category_4->compareName = trim(strtolower($v[8]));
                                        $category_4->class = 4;
                                        $category_4->path =  $category_3->path.'-'.(DB::table('categorys')->max('id')+1);
                                        $category_4->save();         //--分类4
                                    }
                                }
                            }else{

                                //12级分类都存在且path也相同，再判读三级分类是否存在，如果不存在就创建3 4级分类
                                if($v[7]){
                                    $name = Category::query()
                                        ->where('compareName',trim(strtolower($v[7])))
                                        ->where('language_id',$request->language_id)->get();
                                    if (!$name->first()){
                                        $category_2 = Category::query()
                                            ->where('compareName',trim(strtolower($v[6])))
                                            ->where('language_id',$request->language_id)->get()->first();
                                        $category_3 = new Category;
                                        $category_3->parent_id =$category_2->id;
                                        $category_3->brand_id = $request->brand_id;
                                        $category_3->language_id = $request->language_id;
                                        $category_3->name = $v[7];
                                        $category_3->compareName = trim(strtolower($v[7]));
                                        $category_3->class = 3;
                                        $category_3->path = $category_2->path.'-'.(DB::table('categorys')->max('id')+1);
                                        $category_3->save();       //--分类3
                                        if ($v[8]){
                                            $category_4 = new Category;
                                            $category_4->parent_id = $category_3->id;
                                            $category_4->brand_id = $request->brand_id;
                                            $category_4->language_id = $request->language_id;
                                            $category_4->name = $v[8];
                                            $category_4->compareName = trim(strtolower($v[8]));
                                            $category_4->class = 4;
                                            $category_4->path =  $category_3->path.'-'.(DB::table('categorys')->max('id')+1);
                                            $category_4->save();         //--分类4
                                        }
                                    }else{
                                        //如果三级分类也存在在数据库中，判断path
                                        $nowParent = Category::query()
                                            ->where('compareName',trim(strtolower($v[6])))
                                            ->where('language_id',$request->language_id)->get()->first();
                                        $oldParent = $name->where('parent_id',$nowParent->id)->first();
                                        //如果原数据的父级分类的path相同  说明是同一条数据 则继续判断三级分类

                                        if ($oldParent == null) { //不是同一条数据  则创建新分类

                                            $category_3 = new Category;
                                            $category_3->parent_id =$nowParent->id;
                                            $category_3->brand_id = $request->brand_id;
                                            $category_3->language_id = $request->language_id;
                                            $category_3->name = $v[7];
                                            $category_3->compareName = trim(strtolower($v[7]));
                                            $category_3->class = 3;
                                            $category_3->path = $nowParent->path.'-'.(DB::table('categorys')->max('id')+1);;
                                            $category_3->save();       //--分类3
                                            if ($v[8]){
                                                $category_4 = new Category;
                                                $category_4->parent_id = $category_3->id;
                                                $category_4->brand_id = $request->brand_id;
                                                $category_4->language_id = $request->language_id;
                                                $category_4->name = $v[8];
                                                $category_4->compareName = trim(strtolower($v[8]));
                                                $category_4->class = 4;
                                                $category_4->path =  $category_3->path.'-'.(DB::table('categorys')->max('id')+1);
                                                $category_4->save();         //--分类4
                                            }
                                        }else{
                                            if($v[8]){
                                                $name = Category::query()
                                                    ->where('compareName',trim(strtolower($v[8])))
                                                    ->where('language_id',$request->language_id)->get();
                                                if (!$name->first()){
                                                    $category_3 = Category::query()
                                                        ->where('compareName',trim(strtolower($v[7])))
                                                        ->where('language_id',$request->language_id)->get()->first();
                                                    $category_4 = new Category;
                                                    $category_4->parent_id =$category_3->id;
                                                    $category_4->brand_id = $request->brand_id;
                                                    $category_4->language_id = $request->language_id;
                                                    $category_4->name = $v[8];
                                                    $category_4->compareName = trim(strtolower($v[8]));
                                                    $category_4->class = 4;
                                                    $category_4->path = $category_3->path.'-'.(DB::table('categorys')->max('id')+1);
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

            }
        }

        //product_descriptions表操作
        foreach ($res as $k=>$v){
            $product_descriptions[$k]['product_model'] = $v[0];
            $product_descriptions[$k]['language_id'] = $request->language_id;
            $product_descriptions[$k]['brand_id'] = $request->brand_id;
            $product_descriptions[$k]['product_name'] = $v[1];
            $product_descriptions[$k]['category_1'] = Category::query()
                                                     ->where('compareName',trim(strtolower($v[5])))
                                                     ->where('language_id',$request->language_id)->get()->first()->id;
            if ($v[6]){
                $product_descriptions[$k]['category_2'] = Category::query()
                    ->where('compareName',trim(strtolower($v[6])))
                    ->where('parent_id',$product_descriptions[$k]['category_1'])
                    ->where('language_id',$request->language_id)->get()->first()->id;
            }else{
                $product_descriptions[$k]['category_2'] = null;
            }
            if ($v[7]){
                $product_descriptions[$k]['category_3'] = Category::query()
                    ->where('compareName',trim(strtolower($v[7])))
                    ->where('parent_id',$product_descriptions[$k]['category_2'])
                    ->where('language_id',$request->language_id)->get()->first()->id;
            }else{
                $product_descriptions[$k]['category_3'] = null;
            }
            if ($v[8]){
                $product_descriptions[$k]['category_4'] = Category::query()
                    ->where('compareName',trim(strtolower($v[8])))
                    ->where('parent_id',$product_descriptions[$k]['category_3'])
                    ->where('language_id',$request->language_id)->get()->first()->id;
            }else{
                $product_descriptions[$k]['category_4'] = null;
            }
            $path_2 = $product_descriptions[$k]['category_2'] ? '-'.$product_descriptions[$k]['category_2'] : '';
            $path_3 = $product_descriptions[$k]['category_3'] ? '-'.$product_descriptions[$k]['category_3'] : '';
            $path_4 = $product_descriptions[$k]['category_4'] ? '-'.$product_descriptions[$k]['category_4'] : '';
            $product_descriptions[$k]['path'] = $product_descriptions[$k]['category_1'].$path_2.$path_3.$path_4;
            $product_descriptions[$k]['product_description'] = $v[9];
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

        //products表操作
        foreach ($res as $k=>$v){
            $products[$k]['model'] = $v[0];
            $products[$k]['special_price'] = $v[4];
            $products[$k]['price'] = $v[3];
            $products[$k]['image'] = $v[2];

            //判断当前分类  默认存在一级分类
            $category_id = Category::where('name',$v[5])->first();

            if(!$category_id){
                return redirect()->back()->with('cateIsNull',1);
            }
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

        return redirect()->back()->with('success','上传成功');


    }

    public function testResult(Request $request)
    {

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
//        dd($res);

        $ExcelName = time().'-'.$request->file('file')->getClientOriginalName();
        $path = $request->file('file')->storeAs('allExcel',$request->language_id.'/'.$ExcelName);

        UploadLog::query()->create(['type'=>'数据表','fileName'=>$ExcelName,'language_id'=>$request->language_id,'brand_id'=>$request->brand_id]);

        //categorys表操作   11 12 13 14以次为model model+lan path active 0为否(更新) 1为是(添加)
        foreach ($res as $k=>$v){
            if($res[$k][4] == null){
                $res[$k][4] = (($v[12]+$v[13])/0.45)/6.5;
            }
            $res[$k][15] = $v[11];
            $res[$k][16] = $v[12];//成本价
            $res[$k][17] = $v[13];//邮费
            if ($v[5]){
                // 1. 先判断该分类是否存在   --分类1
                $name = Category::query()
                    ->where('compareName',trim(strtolower($v[5])))
                    ->where('language_id',$request->language_id)->get();

                if (!$name->first()){  //该name不存在,插入此条完整数据
                    //path不存在 一级分类不存在
                    //判断model是否存在
                    $model = Product::query()
                        ->where('model',$v[0])->get();
                    if (!$model->first()){
                        //不存在该model 0 也不存在model+lan 0 path 0 添加
                        $res[$k][11] = 0;
                        $res[$k][12] = 0;
                        $res[$k][13] = 0;
                        $res[$k][14] = 1;

                    }else{
                        //存在该model  再判断是否存在其他语言
                        $model_lan = Product_description::query()
                            ->where('product_model',$v[0])
                            ->where('language_id',$request->language_id)->get();
                        if (!$model_lan->first()){
                            //model 1 model_lan 0 path 0 添加
                            $res[$k][11] = 1;
                            $res[$k][12] = 0;
                            $res[$k][13] = 0;
                            $res[$k][14] = 1;
                        }else{
                            //model 1 model_lan 1 path 0 添加
                            $res[$k][11] = 1;
                            $res[$k][12] = 1;
                            $res[$k][13] = 0;
                            $res[$k][14] = 1;
                        }
                    }
                }else{
                    // 2. 该分类已经存在 查找后面分类  --分类1

                    if($v[6]){ //判断上传的表当中是否有2级分类
                        // 先判读二级分类是否存在数据库中，如果不存在就创建2 3 4级分类
                        $name = Category::query()
                            ->where('compareName',trim(strtolower($v[6])))
                            ->where('class',2)
                            ->where('language_id',$request->language_id)->get();
                        if (!$name->first()){ //不存在 创建234级   还没写二级分类下面的path逻辑
                            //path不存在 一级存在，二级分类不存在
                            //判断model是否存在
                            $model = Product::query()
                                ->where('model',$v[0])->get();
                            if (!$model->first()){
                                //不存在该model 0 也不存在model+lan 0 path 0 添加
                                $res[$k][11] = 0;
                                $res[$k][12] = 0;
                                $res[$k][13] = 0;
                                $res[$k][14] = 1;
                            }else{
                                //存在该model  再判断是否存在其他语言
                                $model_lan = Product_description::query()
                                    ->where('product_model',$v[0])
                                    ->where('language_id',$request->language_id)->get();
                                if (!$model_lan->first()){
                                    //model 1 model_lan 0 path 0 添加
                                    $res[$k][11] = 1;
                                    $res[$k][12] = 0;
                                    $res[$k][13] = 0;
                                    $res[$k][14] = 1;
                                }else{
                                    //model 1 model_lan 1 path 0 添加
                                    $res[$k][11] = 1;
                                    $res[$k][12] = 1;
                                    $res[$k][13] = 0;
                                    $res[$k][14] = 1;
                                }
                            }
                        }else{ //如果2级分类也存在,再判断path是否相同
                            $nowParent = Category::query()
                                ->where('compareName',trim(strtolower($v[5])))
                                ->where('language_id',$request->language_id)->get()->first();
                            $oldParent = $name->where('parent_id',$nowParent->id)->first();

                            //如果原数据的父级分类的path相同  说明是同一条数据 则继续判断三级分类
                            if (!$oldParent){ //不是同一条数据  则创建新分类
                                //path不存在 一级存在，二级分类存在,但path不同
                                //判断model是否存在
                                $model = Product::query()
                                    ->where('model',$v[0])->get();
                                if (!$model->first()){
                                    //不存在该model 0 也不存在model+lan 0 path 0 添加
                                    $res[$k][11] = 0;
                                    $res[$k][12] = 0;
                                    $res[$k][13] = 0;
                                    $res[$k][14] = 1;
                                }else{
                                    //存在该model  再判断是否存在其他语言
                                    $model_lan = Product_description::query()
                                        ->where('product_model',$v[0])
                                        ->where('language_id',$request->language_id)->get();
                                    if (!$model_lan->first()){
                                        //model 1 model_lan 0 path 0 添加
                                        $res[$k][11] = 1;
                                        $res[$k][12] = 0;
                                        $res[$k][13] = 0;
                                        $res[$k][14] = 1;
                                    }else{
                                        //model 1 model_lan 1 path 0 添加
                                        $res[$k][11] = 1;
                                        $res[$k][12] = 1;
                                        $res[$k][13] = 0;
                                        $res[$k][14] = 1;
                                    }
                                }
                            }else{
                                //12级分类都存在且path也相同，再判读三级分类是否存在，如果不存在就创建3 4级分类
                                if($v[7]){
                                    $name = Category::query()
                                        ->where('compareName',trim(strtolower($v[7])))
                                        ->where('class',3)
                                        ->where('language_id',$request->language_id)->get();
                                    if (!$name->first()){
                                        //path不存在 一级二级存在，三级分类不存在
                                        //判断model是否存在
                                        $model = Product::query()
                                            ->where('model',$v[0])->get();
                                        if (!$model->first()){
                                            //不存在该model 0 也不存在model+lan 0 path 0 添加
                                            $res[$k][11] = 0;
                                            $res[$k][12] = 0;
                                            $res[$k][13] = 0;
                                            $res[$k][14] = 1;
                                        }else{
                                            //存在该model  再判断是否存在其他语言
                                            $model_lan = Product_description::query()
                                                ->where('product_model',$v[0])
                                                ->where('language_id',$request->language_id)->get();
                                            if (!$model_lan->first()){
                                                //model 1 model_lan 0 path 0 添加
                                                $res[$k][11] = 1;
                                                $res[$k][12] = 0;
                                                $res[$k][13] = 0;
                                                $res[$k][14] = 1;
                                            }else{
                                                //model 1 model_lan 1 path 0 添加
                                                $res[$k][11] = 1;
                                                $res[$k][12] = 1;
                                                $res[$k][13] = 0;
                                                $res[$k][14] = 1;
                                            }
                                        }
                                    }else{
                                        //如果三级分类也存在在数据库中，判断path
                                        $nowParent = Category::query()
                                            ->where('compareName',trim(strtolower($v[6])))
                                            ->where('language_id',$request->language_id)->get()->first();
                                        $oldParent = $name->where('parent_id',$nowParent->id)->first();
                                        //如果原数据的父级分类的path相同  说明是同一条数据 则继续判断三级分类

                                        if ($oldParent == null) { //不是同一条数据  则创建新分类
                                            //path不存在 一二三级分类存在,但path不同
                                            //判断model是否存在
                                            $model = Product::query()
                                                ->where('model',$v[0])->get();
                                            if (!$model->first()){
                                                //不存在该model 0 也不存在model+lan 0 path 0 添加
                                                $res[$k][11] = 0;
                                                $res[$k][12] = 0;
                                                $res[$k][13] = 0;
                                                $res[$k][14] = 1;
                                            }else{
                                                //存在该model  再判断是否存在其他语言
                                                $model_lan = Product_description::query()
                                                    ->where('product_model',$v[0])
                                                    ->where('language_id',$request->language_id)->get();
                                                if (!$model_lan->first()){
                                                    //model 1 model_lan 0 path 0 添加
                                                    $res[$k][11] = 1;
                                                    $res[$k][12] = 0;
                                                    $res[$k][13] = 0;
                                                    $res[$k][14] = 1;
                                                }else{
                                                    //model 1 model_lan 1 path 0 添加
                                                    $res[$k][11] = 1;
                                                    $res[$k][12] = 1;
                                                    $res[$k][13] = 0;
                                                    $res[$k][14] = 1;
                                                }
                                            }
                                        }else{
                                            if($v[8]){
                                                $name = Category::query()
                                                    ->where('compareName',trim(strtolower($v[8])))
                                                    ->where('class',4)
                                                    ->where('language_id',$request->language_id)->get();
                                                if (!$name->first()){
                                                    //path不存在 一级二级三存在，四级分类不存在
                                                    //判断model是否存在
                                                    $model = Product::query()
                                                        ->where('model',$v[0])->get();
                                                    if (!$model->first()){
                                                        //不存在该model 0 也不存在model+lan 0 path 0 添加
                                                        $res[$k][11] = 0;
                                                        $res[$k][12] = 0;
                                                        $res[$k][13] = 0;
                                                        $res[$k][14] = 1;
                                                    }else{
                                                        //存在该model  再判断是否存在其他语言
                                                        $model_lan = Product_description::query()
                                                            ->where('product_model',$v[0])
                                                            ->where('language_id',$request->language_id)->get();
                                                        if (!$model_lan->first()){
                                                            //model 1 model_lan 0 path 0 添加
                                                            $res[$k][11] = 1;
                                                            $res[$k][12] = 0;
                                                            $res[$k][13] = 0;
                                                            $res[$k][14] = 1;
                                                        }else{
                                                            //model 1 model_lan 1 path 0 添加
                                                            $res[$k][11] = 1;
                                                            $res[$k][12] = 1;
                                                            $res[$k][13] = 0;
                                                            $res[$k][14] = 1;
                                                        }
                                                    }
                                                    }else{
                                                    //如果四级分类也存在在数据库中，判断path
                                                    $topParent = Category::query()
                                                        ->where('compareName',trim(strtolower($v[5])))
                                                        ->where('language_id',$request->language_id)->get()->first();
                                                    $cate_3 = $topParent->getChild->where('compareName',trim(strtolower($v[6])))->first()->getChild->where('compareName',trim(strtolower($v[7])))->first();
                                                    $oldParent = $name->where('parent_id',$cate_3->id)->first();
                                                    if (!$oldParent){
                                                        //不存在path  创建新path

                                                        //判断model是否存在
                                                        $model = Product::query()
                                                            ->where('model',$v[0])->get();

                                                        if (!$model->first()){
                                                            //不存在该model 0 也不存在model+lan 0 path 0 添加
                                                            $res[$k][11] = 0;
                                                            $res[$k][12] = 0;
                                                            $res[$k][13] = 0;
                                                            $res[$k][14] = 1;
                                                        }else{
                                                            //存在该model  再判断是否存在其他语言
                                                            $model_lan = Product_description::query()
                                                                ->where('product_model',$v[0])
                                                                ->where('language_id',$request->language_id)->get();
                                                            if (!$model_lan->first()){
                                                                //model 1 model_lan 0 path 0 添加
                                                                $res[$k][11] = 1;
                                                                $res[$k][12] = 0;
                                                                $res[$k][13] = 0;
                                                                $res[$k][14] = 1;
                                                            }else{
                                                                //model 1 model_lan 1 path 0 添加
                                                                $res[$k][11] = 1;
                                                                $res[$k][12] = 1;
                                                                $res[$k][13] = 0;
                                                                $res[$k][14] = 1;
                                                            }
                                                        }

                                                    }else{
                                                    //path 存在
                                                    //判断model是否存在
                                                    $model = Product::query()
                                                        ->where('model',$v[0])->get();
                                                    if (!$model->first()){
                                                        //不存在该model 0 也不存在model+lan 0 path 1 添加
                                                        $res[$k][11] = 0;
                                                        $res[$k][12] = 0;
                                                        $res[$k][13] = 0;
                                                        $res[$k][14] = 1;
                                                    }else{
                                                        //存在该model  再判断是否存在其他语言
                                                        $model_lan = Product_description::query()
                                                            ->where('product_model',$v[0])
                                                            ->where('language_id',$request->language_id)->get();
                                                        if (!$model_lan->first()){
                                                            //model 1 model_lan 0 path 0 添加
                                                            $res[$k][11] = 1;
                                                            $res[$k][12] = 0;
                                                            $res[$k][13] = 0;
                                                            $res[$k][14] = 1;
                                                        }else{
                                                            //model 1 model_lan 1 path 1 更新  判断model path是否唯一
                                                            if ($model_lan->first()->path == $oldParent->path){
                                                                //model 1 model_lan 1 path 1 更新
                                                                $res[$k][11] = 1;
                                                                $res[$k][12] = 1;
                                                                $res[$k][13] = 1;
                                                                $res[$k][14] = 0;
                                                            }else{
                                                                //model 1 model_lan 1 path 0 更新
                                                                $res[$k][11] = 1;
                                                                $res[$k][12] = 1;
                                                                $res[$k][13] = 0;
                                                                $res[$k][14] = 1;
                                                            }
                                                        }
                                                    }
                                                }
                                                }
                                            }else{

                                                //只有一二三级分类 判断path model是否重复
                                                //判断model是否存在
                                                $model = Product::query()
                                                    ->where('model',$v[0])->get();
                                                if (!$model->first()){
                                                    //不存在该model 0 也不存在model+lan 0 path 0 添加
                                                    $res[$k][11] = 0;
                                                    $res[$k][12] = 0;
                                                    $res[$k][13] = 0;
                                                    $res[$k][14] = 1;
                                                }else{
                                                    //存在该model  再判断是否存在其他语言
                                                    $model_lan = Product_description::query()
                                                        ->where('product_model',$v[0])
                                                        ->where('language_id',$request->language_id)->get();
                                                    $oldParent = Category::query()
                                                        ->where('compareName',trim(strtolower($v[5])))
                                                        ->where('language_id',$request->language_id)
                                                        ->get()->first()
                                                        ->getChild
                                                        ->where('compareName',trim(strtolower($v[6])))
                                                        ->first()->getChild
                                                        ->where('compareName',trim(strtolower($v[7])))->first();


                                                    if (!$model_lan->first()){
                                                        //model 1 model_lan 0 path 0 添加
                                                        $res[$k][11] = 1;
                                                        $res[$k][12] = 0;
                                                        $res[$k][13] = 0;
                                                        $res[$k][14] = 1;
                                                    }else{
                                                        //model 1 model_lan 1 path 1 更新  判断model path是否唯一
                                                        if ($model_lan->first()->path == $oldParent->path){
                                                            //model 1 model_lan 1 path 1 更新
                                                            $res[$k][11] = 1;
                                                            $res[$k][12] = 1;
                                                            $res[$k][13] = 1; //此model的此分类重复
                                                            $res[$k][14] = 0;
                                                        }else{
                                                            //model 1 model_lan 1 path 0 更新
                                                            $res[$k][11] = 1;
                                                            $res[$k][12] = 1;
                                                            $res[$k][13] = 0;
                                                            $res[$k][14] = 1;
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                    }
                                }else{
                                    //只有一二级分类 判断path model是否重复

                                    //判断model是否存在
                                    $model = Product::query()
                                        ->where('model',$v[0])->get();
                                    if (!$model->first()){
                                        //不存在该model 0 也不存在model+lan 0 path 0 添加
                                        $res[$k][11] = 0;
                                        $res[$k][12] = 0;
                                        $res[$k][13] = 0;
                                        $res[$k][14] = 1;
                                    }else{
                                        //存在该model  再判断是否存在其他语言
                                        $model_lan = Product_description::query()
                                            ->where('product_model',$v[0])
                                            ->where('language_id',$request->language_id)->get();
                                        $oldParent = Category::query()
                                            ->where('compareName',trim(strtolower($v[5])))
                                            ->where('language_id',$request->language_id)
                                            ->get()->first()->getChild->where('compareName',trim(strtolower($v[6])))->first();

                                        if (!$model_lan->first()){
                                            //model 1 model_lan 0 path 0 添加
                                            $res[$k][11] = 1;
                                            $res[$k][12] = 0;
                                            $res[$k][13] = 0;
                                            $res[$k][14] = 1;
                                        }else{
                                            //model 1 model_lan 1 path 1 更新  判断model path是否唯一
                                            if ($model_lan->first()->path == $oldParent->path){
                                                //model 1 model_lan 1 path 1 更新
                                                $res[$k][11] = 1;
                                                $res[$k][12] = 1;
                                                $res[$k][13] = 1; //此model的此分类重复
                                                $res[$k][14] = 0;
                                            }else{
                                                //model 1 model_lan 1 path 0 更新
                                                $res[$k][11] = 1;
                                                $res[$k][12] = 1;
                                                $res[$k][13] = 0;
                                                $res[$k][14] = 1;
                                            }
                                        }
                                    }
                                }
                            }

                        }
                    }else{
                        //只有一级分类 判断path model是否重复

                        //判断model是否存在
                        $model = Product::query()
                            ->where('model',$v[0])->get();
                        if (!$model->first()){
                            //不存在该model 0 也不存在model+lan 0 path 0 添加
                            $res[$k][11] = 0;
                            $res[$k][12] = 0;
                            $res[$k][13] = 0;
                            $res[$k][14] = 1;
                        }else{
                            //存在该model  再判断是否存在其他语言
                            $model_lan = Product_description::query()
                                ->where('product_model',$v[0])
                                ->where('language_id',$request->language_id)->get();
                            $oldParent = Category::query()
                                ->where('compareName',trim(strtolower($v[5])))
                                ->where('language_id',$request->language_id)
                                ->get()->first();

                            if (!$model_lan->first()){
                                //model 1 model_lan 0 path 0 添加
                                $res[$k][11] = 1;
                                $res[$k][12] = 0;
                                $res[$k][13] = 0;
                                $res[$k][14] = 1;
                            }else{
                                //model 1 model_lan 1 path 1 更新  判断model path是否唯一

                                    if ($model_lan->first()->path == $oldParent->path){
                                        //model 1 model_lan 1 path 1 更新
                                        $res[$k][11] = 1;
                                        $res[$k][12] = 1;
                                        $res[$k][13] = 1; //此model的此分类重复
                                        $res[$k][14] = 0;
                                    }else{

                                        //model 1 model_lan 1 path 0 更新
                                        $res[$k][11] = 1;
                                        $res[$k][12] = 1;
                                        $res[$k][13] = 0;
                                        $res[$k][14] = 1;
                                    }


                            }
                        }
                    }
                }

            }
        }
        Session::put('res',$res);
        Session::put('importType',$request->importType);
//        dd(Session::get('ExcelName'));
        $language_id = $request->language_id;
        $brand_id = $request->brand_id;
        return view('/products/result',compact('res','language_id','brand_id'));

    }

    //上传产品
    public function import2(Request $request){

        $res = Session::get('res');
        $importType = Session::get('importType');

        //categorys表操作
        foreach ($res as $k=>$v){
            if ($v[5]){
                // 1. 先判断该分类是否存在   --分类1
                $name = Category::query()
                    ->where('compareName',trim(strtolower($v[5])))
                    ->where('language_id',$request->language_id)->get();
                if (!$name->first()){  //该name不存在,插入此条完整数据
                    $category = new Category;
                    $category->parent_id = 0;
                    $category->brand_id = $request->brand_id;
                    $category->language_id = $request->language_id;
                    $category->name = $v[5];
                    $category->compareName = trim(strtolower($v[5]));
                    $category->class = 1;
                    $category->path = DB::table('categorys')->max('id')+1;
                    $category->save();       //--分类1

                    if ($v[6]){
                        $category_2 = new Category;
                        $category_2->parent_id = $category->id;
                        $category_2->brand_id = $request->brand_id;
                        $category_2->language_id = $request->language_id;
                        $category_2->name = $v[6];
                        $category_2->compareName = trim(strtolower($v[6]));
                        $category_2->class = 2;
                        $category_2->path =  $category->path.'-'.(DB::table('categorys')->max('id')+1);
                        $category_2->save();       //--分类2
                        if ($v[7]){

                            $category_3 = new Category;
                            $category_3->parent_id = $category_2->id;
                            $category_3->brand_id = $request->brand_id;
                            $category_3->language_id = $request->language_id;
                            $category_3->name = $v[7];
                            $category_3->compareName = trim(strtolower($v[7]));
                            $category_3->class = 3;
                            $category_3->path =  $category_2->path.'-'.(DB::table('categorys')->max('id')+1);
                            $category_3->save();       //--分类3
                            if ($v[8]){

                                $category_4 = new Category;
                                $category_4->parent_id = $category_3->id;
                                $category_4->brand_id = $request->brand_id;
                                $category_4->language_id = $request->language_id;
                                $category_4->name = $v[8];
                                $category_4->compareName = trim(strtolower($v[8]));
                                $category_4->class = 4;
                                $category_4->path =  $category_3->path.'-'.(DB::table('categorys')->max('id')+1);
                                $category_4->save();         //--分类4
                            }
                        }
                    }



                }else{
                    // 2. 该分类已经存在 查找后面分类  --分类1

                    if($v[6]){ //判断上传的表当中是否有2级分类
                        // 先判读二级分类是否存在数据库中，如果不存在就创建2 3 4级分类
                        $name = Category::query()
                            ->where('compareName',trim(strtolower($v[6])))
                            ->where('language_id',$request->language_id)->get();
                        if (!$name->first()){ //不存在 创建234级   还没写二级分类下面的path逻辑
                            $category_2 = new Category;
                            $category_1 = Category::query()
                                ->where('compareName',trim(strtolower($v[5])))
                                ->where('language_id',$request->language_id)->get()->first();
                            $category_2->parent_id =$category_1->id;
                            $category_2->brand_id = $request->brand_id;
                            $category_2->language_id = $request->language_id;
                            $category_2->name = $v[6];
                            $category_2->compareName = trim(strtolower($v[6]));
                            $category_2->class = 2;
                            $category_2->path = $category_1->path.'-'.(DB::table('categorys')->max('id')+1);;
                            $category_2->save();       //--分类2
                            if ($v[7]){
                                $category_3 = new Category;
                                $category_3->parent_id = $category_2->id;
                                $category_3->brand_id = $request->brand_id;
                                $category_3->language_id = $request->language_id;
                                $category_3->name = $v[7];
                                $category_3->compareName = trim(strtolower($v[7]));
                                $category_3->class = 3;
                                $category_3->path =  $category_2->path.'-'.(DB::table('categorys')->max('id')+1);
                                $category_3->save();       //--分类3
                                if ($v[8]){
                                    $category_4 = new Category;
                                    $category_4->parent_id = $category_3->id;
                                    $category_4->brand_id = $request->brand_id;
                                    $category_4->language_id = $request->language_id;
                                    $category_4->name = $v[8];
                                    $category_4->compareName = trim(strtolower($v[8]));
                                    $category_4->class = 4;
                                    $category_4->path =  $category_3->path.'-'.(DB::table('categorys')->max('id')+1);
                                    $category_4->save();         //--分类4
                                }
                            }
                        }else{ //如果2级分类也存在,再判断path是否相同
                            $nowParent = Category::query()
                                ->where('compareName',trim(strtolower($v[5])))
                                ->where('language_id',$request->language_id)->get()->first();
                            $oldParent = $name->where('parent_id',$nowParent->id)->first();

                            //如果原数据的父级分类的path相同  说明是同一条数据 则继续判断三级分类

                            if (!$oldParent){ //不是同一条数据  则创建新分类
                                $category_2 = new Category;
                                $category_2->parent_id =$nowParent->id;
                                $category_2->brand_id = $request->brand_id;
                                $category_2->language_id = $request->language_id;
                                $category_2->name = $v[6];
                                $category_2->compareName = trim(strtolower($v[6]));
                                $category_2->class = 2;
                                $category_2->path = $nowParent->path.'-'.(DB::table('categorys')->max('id')+1);;
                                $category_2->save();       //--分类2
                                if ($v[7]){
                                    $category_3 = new Category;
                                    $category_3->parent_id = $category_2->id;
                                    $category_3->brand_id = $request->brand_id;
                                    $category_3->language_id = $request->language_id;
                                    $category_3->name = $v[7];
                                    $category_3->compareName = trim(strtolower($v[7]));
                                    $category_3->class = 3;
                                    $category_3->path =  $category_2->path.'-'.(DB::table('categorys')->max('id')+1);
                                    $category_3->save();       //--分类3
                                    if ($v[8]){
                                        $category_4 = new Category;
                                        $category_4->parent_id = $category_3->id;
                                        $category_4->brand_id = $request->brand_id;
                                        $category_4->language_id = $request->language_id;
                                        $category_4->name = $v[8];
                                        $category_4->compareName = trim(strtolower($v[8]));
                                        $category_4->class = 4;
                                        $category_4->path =  $category_3->path.'-'.(DB::table('categorys')->max('id')+1);
                                        $category_4->save();         //--分类4
                                    }
                                }
                            }else{

                                //12级分类都存在且path也相同，再判读三级分类是否存在，如果不存在就创建3 4级分类
                                if($v[7]){
                                    $name = Category::query()
                                        ->where('compareName',trim(strtolower($v[7])))
                                        ->where('language_id',$request->language_id)->get();
                                    if (!$name->first()){
                                        $category_2 = Category::query()
                                            ->where('compareName',trim(strtolower($v[6])))
                                            ->where('language_id',$request->language_id)->get()->first();
                                        $category_3 = new Category;
                                        $category_3->parent_id =$category_2->id;
                                        $category_3->brand_id = $request->brand_id;
                                        $category_3->language_id = $request->language_id;
                                        $category_3->name = $v[7];
                                        $category_3->compareName = trim(strtolower($v[7]));
                                        $category_3->class = 3;
                                        $category_3->path = $category_2->path.'-'.(DB::table('categorys')->max('id')+1);
                                        $category_3->save();       //--分类3
                                        if ($v[8]){
                                            $category_4 = new Category;
                                            $category_4->parent_id = $category_3->id;
                                            $category_4->brand_id = $request->brand_id;
                                            $category_4->language_id = $request->language_id;
                                            $category_4->name = $v[8];
                                            $category_4->compareName = trim(strtolower($v[8]));
                                            $category_4->class = 4;
                                            $category_4->path =  $category_3->path.'-'.(DB::table('categorys')->max('id')+1);
                                            $category_4->save();         //--分类4
                                        }
                                    }else{
                                        //如果三级分类也存在在数据库中，判断path
                                        $nowParent = Category::query()
                                            ->where('compareName',trim(strtolower($v[6])))
                                            ->where('language_id',$request->language_id)->get()->first();
                                        $oldParent = $name->where('parent_id',$nowParent->id)->first();
                                        //如果原数据的父级分类的path相同  说明是同一条数据 则继续判断三级分类

                                        if ($oldParent == null) { //不是同一条数据  则创建新分类

                                            $category_3 = new Category;
                                            $category_3->parent_id =$nowParent->id;
                                            $category_3->brand_id = $request->brand_id;
                                            $category_3->language_id = $request->language_id;
                                            $category_3->name = $v[7];
                                            $category_3->compareName = trim(strtolower($v[7]));
                                            $category_3->class = 3;
                                            $category_3->path = $nowParent->path.'-'.(DB::table('categorys')->max('id')+1);;
                                            $category_3->save();       //--分类3
                                            if ($v[8]){
                                                $category_4 = new Category;
                                                $category_4->parent_id = $category_3->id;
                                                $category_4->brand_id = $request->brand_id;
                                                $category_4->language_id = $request->language_id;
                                                $category_4->name = $v[8];
                                                $category_4->compareName = trim(strtolower($v[8]));
                                                $category_4->class = 4;
                                                $category_4->path =  $category_3->path.'-'.(DB::table('categorys')->max('id')+1);
                                                $category_4->save();         //--分类4
                                            }
                                        }else{
                                            if($v[8]){
                                                $name = Category::query()
                                                    ->where('compareName',trim(strtolower($v[8])))
                                                    ->where('language_id',$request->language_id)->get();
                                                if (!$name->first()){
                                                    $category_3 = Category::query()
                                                        ->where('compareName',trim(strtolower($v[7])))
                                                        ->where('language_id',$request->language_id)->get()->first();
                                                    $category_4 = new Category;
                                                    $category_4->parent_id =$category_3->id;
                                                    $category_4->brand_id = $request->brand_id;
                                                    $category_4->language_id = $request->language_id;
                                                    $category_4->name = $v[8];
                                                    $category_4->compareName = trim(strtolower($v[8]));
                                                    $category_4->class = 4;
                                                    $category_4->path = $category_3->path.'-'.(DB::table('categorys')->max('id')+1);
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

            }
        }

        //上传多语言表时 不用操作products表
        if($importType == 1){
            //products表操作
            $products = array();
            $updateArrs = array();
            foreach ($res as $k=>$v){

                if ($v[11] == 1){
                    //model 重复 product模型一定是更新
                    $updateArrs[$k]['model'] = $v[0];
                    $updateArrs[$k]['special_price'] = $v[4];
                    $updateArrs[$k]['price'] = $v[3];
                    $updateArrs[$k]['costPrice'] = $v[16];//成本价
                    $updateArrs[$k]['freight'] = $v[17];//邮费
                    $updateArrs[$k]['image'] = $v[2];
                    $updateArrs[$k]['addImages'] = $v[10];
                    $updateArrs[$k]['created_at'] = date('Y-m-d H:i:s', time());
                    $updateArrs[$k]['updated_at'] = date('Y-m-d H:i:s', time());
//                $result = Product::query()->where('model',$updateArrs[$k]['model'])->update($updateArrs[$k]);
                }else {
                    $products[$k]['model'] = $v[0];
                    $products[$k]['special_price'] = $v[4];
                    $products[$k]['price'] = $v[3];
                    $products[$k]['costPrice'] = $v[16];//成本价
                    $products[$k]['freight'] = $v[17];//邮费
                    $products[$k]['image'] = $v[2];
                    $products[$k]['addImages'] = $v[10];
                    $products[$k]['created_at'] = date('Y-m-d H:i:s', time());
                    $products[$k]['updated_at'] = date('Y-m-d H:i:s', time());
                    //验证
                    $validator = Validator::make($products[$k], [
                        'special_price' => 'nullable',
                        'price' => 'required',
                        'image' => 'nullable|string',
                    ]);
                    if ($validator->fails()) {
                        return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
                    }
                }
            }
            if (count($updateArrs)>0) {
                $ress = $this->updateBatch('products',$updateArrs);
            }
            if (count($products)>0){
                DB::table('products')->insert($products);
            }
        }


        $product_descriptions = array();
        $product_descriptions_2 = array();
        //product_descriptions表操作
        foreach ($res as $k=>$v) {
            if ($v[14] == 1) {
                //model+lan不重复 添加操作
                $product_descriptions[$k]['product_model'] = $v[0];
                $product_descriptions[$k]['language_id'] = $request->language_id;
                $product_descriptions[$k]['brand_id'] = $request->brand_id;
                $product_descriptions[$k]['product_name'] = $v[1];
                $product_descriptions[$k]['category_1'] = Category::query()
                    ->where('compareName',trim(strtolower($v[5])))
                    ->where('language_id', $request->language_id)
                    ->get()->first()->id;

                if ($v[6]) {
                    $product_descriptions[$k]['category_2'] = Category::query()
                        ->where('compareName',trim(strtolower($v[6])))
                        ->where('parent_id', $product_descriptions[$k]['category_1'])
                        ->where('language_id', $request->language_id)->get()->first()->id;
                } else {
                    $product_descriptions[$k]['category_2'] = null;
                }
                if ($v[7]) {
                    $product_descriptions[$k]['category_3'] = Category::query()
                        ->where('compareName',trim(strtolower($v[7])))
                        ->where('parent_id', $product_descriptions[$k]['category_2'])
                        ->where('language_id', $request->language_id)->get()->first()->id;
                } else {
                    $product_descriptions[$k]['category_3'] = null;
                }
                if ($v[8]) {
                    $product_descriptions[$k]['category_4'] = Category::query()
                        ->where('compareName',trim(strtolower($v[8])))
                        ->where('parent_id', $product_descriptions[$k]['category_3'])
                        ->where('language_id', $request->language_id)->get()->first()->id;
                } else {
                    $product_descriptions[$k]['category_4'] = null;
                }
                $path_2 = $product_descriptions[$k]['category_2'] ? '-' . $product_descriptions[$k]['category_2'] : '';
                $path_3 = $product_descriptions[$k]['category_3'] ? '-' . $product_descriptions[$k]['category_3'] : '';
                $path_4 = $product_descriptions[$k]['category_4'] ? '-' . $product_descriptions[$k]['category_4'] : '';
                $product_descriptions[$k]['is_usable'] = '1';
                $product_descriptions[$k]['path'] = $product_descriptions[$k]['category_1'] . $path_2 . $path_3 . $path_4;
                $product_descriptions[$k]['product_description'] = $v[9];
                $product_descriptions[$k]['created_at'] = date('Y-m-d H:i:s', time());
                $product_descriptions[$k]['updated_at'] = date('Y-m-d H:i:s', time());


                //验证
                $validator = Validator::make($product_descriptions[$k], [
                    'product_model' => 'required',
                    'language_id' => 'required',
                    'product_name' => 'required',
                    'product_description' => 'nullable',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }
            }else{
                $product_descriptions_2[$k]['product_model'] = $v[0];
                $product_descriptions_2[$k]['language_id'] = $request->language_id;
                $product_descriptions_2[$k]['brand_id'] = $request->brand_id;
                $product_descriptions_2[$k]['product_name'] = $v[1];
                $product_descriptions_2[$k]['is_usable'] = '1';
//                $product_descriptions_2[$k]['product_description'] = $v[9];
                $product_descriptions_2[$k]['created_at'] = date('Y-m-d H:i:s', time());
                $product_descriptions_2[$k]['updated_at'] = date('Y-m-d H:i:s', time());


            }
        }
        if(count($product_descriptions) > 0){
            DB::table('product_descriptions')->insert($product_descriptions);
        }elseif (count($product_descriptions_2) > 0){
            $ress = $this->updateBatch_2('product_descriptions',$product_descriptions_2);
        }

        return redirect('products/brand')->with('success',1);

    }

    //同时更新多个记录，参数，表名，数组（别忘了在一开始use DB;）
    public function updateBatch($tableName = "", $multipleData = array()){

        if( $tableName && !empty($multipleData) ) {

            // column or fields to update
            $updateColumn = array_keys($multipleData[1]);
            $referenceColumn = $updateColumn[0]; //e.g id
            unset($updateColumn[0]);
            $whereIn = "";

            $q = "UPDATE ".$tableName." SET ";
            foreach ( $updateColumn as $uColumn ) {
                $q .=  $uColumn." = CASE ";

                foreach( $multipleData as $data ) {
                    $q .= "WHEN ".$referenceColumn." = ".$data[$referenceColumn]." THEN '".$data[$uColumn]."' ";
                }
                $q .= "ELSE ".$uColumn." END, ";
            }
            foreach( $multipleData as $data ) {
                $whereIn .= "'".$data[$referenceColumn]."', ";
            }
            $q = rtrim($q, ", ")." WHERE ".$referenceColumn." IN (".  rtrim($whereIn, ', ').")";

            // Update
            return DB::update(DB::raw($q));

        } else {
            return false;
        }

    }

    public function updateBatch_2($tableName = "", $multipleData = array()){

        if( $tableName && !empty($multipleData) ) {
            // column or fields to update
            $updateColumn = array_keys($multipleData[1]);
            $referenceColumn = $updateColumn[0]; //e.g id
            $referenceColumn_1 = $updateColumn[1]; //e.g id
            unset($updateColumn[0]);
            $whereIn = "";

            $q = "UPDATE ".$tableName." SET ";
            foreach ( $updateColumn as $uColumn ) {
                $q .=  $uColumn." = CASE ";

                foreach( $multipleData as $data ) {
                    $q .= "WHEN ".$referenceColumn." = ".$data[$referenceColumn]." AND ".$referenceColumn_1." = ".$data[$referenceColumn_1]." THEN '".$data[$uColumn]."' ";
                }
                $q .= "ELSE ".$uColumn." END, ";
            }
            foreach( $multipleData as $data ) {
                $whereIn .= "'".$data[$referenceColumn]."', ";
            }
            $q = rtrim($q, ", ")." WHERE ".$referenceColumn." IN (".  rtrim($whereIn, ', ').")";

            // Update
            return DB::update(DB::raw($q));

        } else {
            return false;
        }

    }

    public function getProduct(Request $request)
    {
       $product_descriptions = Product_description::query()
           ->where('product_model',$request->product_model)
           ->where('language_id',$request->language_id)->first();
       $result['is_usable'] = $product_descriptions->is_usable;
       $result['status'] = $product_descriptions->status;
       $result['product_name'] = $product_descriptions->product_name;
       $result['price'] = $product_descriptions->isProduct->price;
       $result['costPrice'] = $product_descriptions->isProduct->costPrice;
       $result['freight'] = $product_descriptions->isProduct->freight;
       $result['special_price'] = $product_descriptions->isProduct->special_price;
       $result['product_description'] = $product_descriptions->product_description;
       return $result;
    }
    public function getLog(Request $request)
    {
        $product_logs = Product_log::query()->where('model',$request->model)->get();
        return $product_logs;
    }
}
