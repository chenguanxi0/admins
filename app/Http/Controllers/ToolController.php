<?php

namespace App\Http\Controllers;

use App\MyClass\timeDeal;
use App\Brand;
use App\Seo;
use App\Category;
use App\Product;
use Illuminate\Support\Facades\App;
use App\Commit;
use App\Language;
use App\UploadLog;
use App\Web;
use Chumper\Zipper\Facades\Zipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use App\classic\DbManage;
use Mail;
use Illuminate\Support\Facades\Storage;

class ToolController extends Controller
{
    public function index()
    {
        return view('tool/index');
    }

    public function uploadList()
    {
        $uploadLogs = UploadLog::all();
        return view('tool/uploadList', compact('uploadLogs'));
    }

    public function logDelete(UploadLog $uploadLog)
    {
        $uploadLog->delete();
        return back()->with('delete', 1);
    }

    public function uploadImages(Request $request)
    {

        if ($request->method() == 'GET') {
            return view('tool/uploads');
        }
        $name = Input::file('uploadfile')->getClientOriginalName();
        $path = $request->file('uploadfile')->storeAs('brands', $name);
        return redirect()->back()->with('upload', 1);
    }

    public function category(Request $request)
    {
        $cd = Category_description::where('name', 'Adidas')->first();
//        dd($cd->findCategory->allChildrenCategorys);

        $categorys_1 = Category::with('allChildrenCategorys')->find(1);
        $categorys_1 = $categorys_1->toArray();
        $res[0] = $categorys_1;
        $categorys = $this->tree2html($res);
        return view('tool/category', compact('categorys'));

    }

    public function readme()
    {
        return view('tool/readme');
    }

    //
    public function tree2html($tree)
    {
        echo '<ul>';
        foreach ($tree as $leaf) {
            echo '<li>' . '<a href=' . $leaf['id'] . '>' . $leaf['name'] . '</a>';
            if (!empty($leaf['all_children_categorys'])) $this->tree2html($leaf['all_children_categorys']);
            echo '</li>';
        }
        echo '</ul>';
    }

    public function getChildId()
    {

    }

    public function commit(Request $request)
    {

        //1.首先查询网站的语言
        $web = Web::query()->where('url', str_replace('www.', '', $request->url))->first();
        //2.调用相应语言的model的评论
        if ($web) {

            $language_id = $web->language_id;
            $brand_id = $web->brand_id;

            $num = Product::query()
                ->where('model', $request->model)
                ->first()->commitsNum;

            $modelCommits = Commit::query()
                ->where('model', $request->model)
                ->where('language_id', $language_id)
                ->orderBy('id', 'desc')->get()->take($num);

            return response()->json($modelCommits);
        }

    }

    public function updateBatch($tableName = "", $multipleData = array())
    {

        if ($tableName && !empty($multipleData)) {

            // column or fields to update
            $updateColumn = array_keys($multipleData[0]);
            $referenceColumn = $updateColumn[0]; //e.g id
            unset($updateColumn[0]);
            $whereIn = "";

            $q = "UPDATE " . $tableName . " SET ";
            foreach ($updateColumn as $uColumn) {
                $q .= $uColumn . " = CASE ";

                foreach ($multipleData as $data) {
                    $q .= "WHEN " . $referenceColumn . " = " . $data[$referenceColumn] . " THEN '" . $data[$uColumn] . "' ";
                }
                $q .= "ELSE " . $uColumn . " END, ";
            }
            foreach ($multipleData as $data) {
                $whereIn .= "'" . $data[$referenceColumn] . "', ";
            }
            $q = rtrim($q, ", ") . " WHERE " . $referenceColumn . " IN (" . rtrim($whereIn, ', ') . ")";

            // Update
            return DB::update(DB::raw($q));

        } else {
            return false;
        }

    }

    public function backupsql()
    {

        $mysql = Config::get('database.connections.mysql'); //从配置文件中获取数据库信息
        //分别是主机，用户名，密码，数据库名，数据库编码
        $db = new DBManage ('localhost', $mysql['username'], $mysql['password'], $mysql['database'], 'utf8');
        // 参数：备份哪个表(可选),备份目录(可选，默认为backup),分卷大小(可选,默认2000，即2M)

        $filename = '/storage/sql/' . $db->backup('', 'storage/sql/', 2000);

        return $filename;
    }

    public function backupbtn()
    {
        $filename = $this->backupsql();
        return view('tool/backupbtn', compact('filename'));
    }

    public function brandAdd(Request $request)
    {
        if ($request->method() == 'GET') {
            return view('tool/brandAdd');
        }
        $this->validate($request, [
            'name' => 'string|required|unique:brands'
        ]);
        $brand = new Brand;
        $brand->name = $request->name;
        $brand->save();
        return redirect()->back()->with('success', 1);
    }

    public function languageAdd(Request $request)
    {
        if ($request->method() == 'GET') {
            return view('tool/languageAdd');
        }
        $this->validate($request, [
            'name' => 'string|required|unique:languages',
            'code' => 'string|required|unique:languages',
        ]);
        $language = new Language;
        $language->name = $request->name;
        $language->code = $request->code;
        $language->save();
        return redirect()->back()->with('success', 1);
    }

    public function zip()
    {
        $filename = str_replace('\\', '/', public_path()) . '/storage/mk/2017032211095413531.jpg';
        Zipper::make('storage/zip/test.zip')->add($filename)->close();

    }

    public function optionsAdd(Request $request)
    {
        if ($request->method() == 'GET') {
            return view('tool.optionsAdd');
        }

        if (!$request->hasFile('file')) {
            exit('上传文件为空！');
        }
        $file = $_FILES;
        $excel_file_path = $file['file']['tmp_name'];
        $excel = App::make('excel');//excel类
        $excel->load($excel_file_path, function ($reader) use (&$res) {
            $reader = $reader->getSheet(0);
            $res = $reader->toArray();
            unset($res[0]);//去除表头
        });

        $url = $request->web;
        $optionName = $request->optionName;
        $language = $request->language;
        //products_options  第一张表
        $sql_1 = "INSERT INTO `products_options`(`products_options_id`,`language_id`, `products_options_name`) VALUES (0," . $language . ",'" . $optionName . "')";

        //products_options_values  第二章张表
        //products_options_values_to_products_options 第三张表
        $optionValuesArrs = array();
        $modelArrs = array();
        foreach ($res as $k => $v) {
            $modelArrs[$k]['model'] = $v[0];
            $singleArrs = explode('|', $v[1]);
            $modelArrs[$k]['option_value'] = $singleArrs;
            foreach ($singleArrs as $kk => $vv) {
                if (!in_array($vv, $optionValuesArrs)) {
                    array_push($optionValuesArrs, $vv);
                }
            }
        }
        $sql_2 = "INSERT INTO `products_options_values` (`products_options_values_id`,`language_id`, `products_options_values_name`, `products_options_values_sort_order`) VALUES (1," . $language . ",'--- please select ---',1)";
        $sql_3 = "INSERT INTO `products_options_values_to_products_options` ( `products_options_id`, `products_options_values_id`) VALUES (0,0)";
        for ($i = 1; $i <= count($optionValuesArrs); $i++) {
            $sql_2 .= ",('" . ($i + 1) . "'," . $language . ",'" . $optionValuesArrs[$i - 1] . "'," . "'" . ($i + 1) . "'" . ")";
            $sql_3 .= ",(0," . $i . ")";
        }
        $ch = curl_init();
        

        /***在这里需要注意的是，要提交的数据不能是二维数组或者更高
         *例如array('name'=>serialize(array('tank','zhang')),'sex'=>1,'birth'=>'20101010')
         *例如array('name'=>array('tank','zhang'),'sex'=>1,'birth'=>'20101010')这样会报错的*/
        $data = array('sql_1' => $sql_1, 'sql_2' => $sql_2, 'sql_3' => $sql_3, 'modelArrs' => json_encode($modelArrs), 'optionValuesArrs' => json_encode($optionValuesArrs));

        curl_setopt($ch, CURLOPT_URL, $url . '/options.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($ch);

        curl_close($ch);
        if ($result) {
            return back();
        } else {
            return false;
        }

    }

    public function imdpay(Request $request)
    {
        if ($request->method() == 'GET') {
            return view('tool.imdpay');
        }
        $urls = explode('|', $request->urls);
        $path = $request->path;
        $content = $request->content;
        $data = array('path' => $path, 'content' => $content);

        foreach ($urls as $url) {

            $realUrl = $url . '/imdapi.php';

            $result = $this->send3rdRequest($data, $realUrl);
            if ($result == 0) {
                return back()->with('fail', $url . '修改无效,请检查');
            }
        }
        return back()->with('message', '修改成功!');
    }

    public function curl($url, $data)
    {

        //初使化init方法
        $ch = curl_init();

        //指定URL
        curl_setopt($ch, CURLOPT_URL, $url);

        //设定请求后返回结果
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //声明使用POST方式来进行发送
        curl_setopt($ch, CURLOPT_POST, 1);

        //发送什么数据呢
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        //忽略证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        //忽略header头信息
        curl_setopt($ch, CURLOPT_HEADER, 0);

        //设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        //发送请求
        $output = curl_exec($ch);

        //关闭curl
        curl_close($ch);

        //返回数据
        return $output;
    }

    public function sqlApi(Request $request)
    {
        if ($request->method() == 'GET') {
            return view('tool.sqlApi');
        }
        $params = array('sql' => $request->sql);
        $url = $request->url . '/sqlApi.php';
        $result = $this->send3rdRequest($params, $url);
        if ($result) {
            return back()->with('message', '修改成功!');
        } else {
            return back()->with('fail', '修改无效,请检查');
        }

    }

    public function statusApi($status, $orderId, $md5, $urls)
    {
        if (strtolower($md5) != strtolower(md5($urls . 'BC5E3ED3-DE70-44b1-B55C-ED31FC5FF368'))) {
            $msg = 'failed';
            return $msg;
        }
        $sql = "UPDATE `orders` SET `orders_status` = " . $status . " WHERE `orders_id` = " . $orderId;
        $params = array('sql' => $sql);
        $url = 'http://www.' . $urls . '/sqlApi.php';
        $result = $this->send3rdRequest($params, $url);
        if ($result) {
            $msg = 'success';
            return $msg;
        } else {
            $msg = 'failed';
            return $msg;
        }
    }
    public function updateSeo()
    {

    }
    public function send3rdRequest($params, $url)
    {
        $randomStr = str_random(6);
        $timeStamp = time();
        $params['signature'] = $this->generateSignature($randomStr, $timeStamp, $params);
        $params['random_str'] = $randomStr;
        $params['time_stamp'] = $timeStamp;
        $result = $this->curl($url, $params);
//        dd($result);
        return $result;
    }

    /**
     * 创建签名
     *
     * @param string $randomStr
     * @param int $timeStamp
     * @param array $otherParams
     * @return string
     */
    public function generateSignature($randomStr, $timeStamp, array $otherParams)
    {
        $arr = [$randomStr, $timeStamp];
        array_push($arr, ...array_values($otherParams));

        sort($arr, SORT_STRING);

        return sha1(implode('', $arr));
    }
	public function email($m,$u,$s){
		
		Mail::raw($u , function ($message) use ($m,$s) {
            $to = '491788533@qq.com';
            $t = $s==1?"成功!":"失败!";
            $message ->to($to)->subject( $t."  ".$m);

        });
		
	}
    public function webShell(Request $request)
    {
        if ($request->method() == 'GET') {
            return view('tool.webShell');
        }
        /**********  接受参数 **********/
        $same = $request->same;
        $urlA = $request->urlA;
        $urlB = $request->urlB;
        $passwdA = $request->passwdA;
        $passwdB = $request->passwdB;
        $dbnameA = $request->dbnameA;
        $dbnameB = $request->dbnameB;
        $urlArr = explode('.',$urlA);
        $aHeaderName = $urlArr[0];
        $aFooterName = $urlArr[1];
        $urlArr = explode('.',$urlB);
        $bHeaderName = $urlArr[0];
        $bFooterName = $urlArr[1];
        if(is_null($dbnameA)){ 
            $dbnameA = $aHeaderName;
        }
        if(is_null($dbnameB)){
            $dbnameB = $bHeaderName;
        }

        /**********  判断是否为同一服务器 **********/
        if ($same == 1) {
            //同一台服务器
            $passwdB = $passwdA;
            $str = "
同服务器\r\n
  
cd /www/web/".$aHeaderName."_".$aFooterName."/public_html && 
  rm -rf *.sql *.gz && 
  tar -zcvf zc.gz ./* && 
  cp -f zc.gz .htaccess /www/web/".$bHeaderName."_".$bFooterName."/public_html && 
  mysqldump -u root -p".$passwdA." ".$dbnameA.">zc.sql && 
  mysql -u root -p".$passwdB." ".$dbnameB."<zc.sql && 
  rm -rf *.sql *.gz && 
  cd /www/web/".$bHeaderName."_".$bFooterName."/public_html && 
  rm -rf errpage/ index.html && 
  tar -zxvf zc.gz && 
  rm -rf zc.gz && 
  sed -i 's/".$urlA."/".$urlB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/.htaccess && 
  sed -i '/DB_DATABASE/s/".$dbnameA."/".$dbnameB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/includes/configure.php && 
  sed -i '/DB_DATABASE/s/".$dbnameA."/".$dbnameB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/whbost/includes/configure.php || 
  sed -i '/DB_DATABASE/s/".$dbnameA."/".$dbnameB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/fly365/includes/configure.php || 
  sed -i '/DB_DATABASE/s/".$dbnameA."/".$dbnameB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/biubiu/includes/configure.php && 
  sed -i 's/".$aHeaderName."/".$bHeaderName."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/includes/configure.php && 
  sed -i 's/".$aHeaderName."/".$bHeaderName."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/whbost/includes/configure.php || 
  sed -i 's/".$aHeaderName."/".$bHeaderName."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/fly365/includes/configure.php || 
  sed -i 's/".$aHeaderName."/".$bHeaderName."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/biubiu/includes/configure.php && 
  sed -i 's/".$aFooterName."/".$bFooterName."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/includes/configure.php && 
  sed -i 's/".$aFooterName."/".$bFooterName."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/whbost/includes/configure.php || 
  sed -i 's/".$aFooterName."/".$bFooterName."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/fly365/includes/configure.php || 
  sed -i 's/".$aFooterName."/".$bFooterName."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/biubiu/includes/configure.php && 
  sed -i 's/".$passwdA."/".$passwdB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/includes/configure.php && 
  sed -i 's/".$passwdA."/".$passwdB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/whbost/includes/configure.php || 
  sed -i 's/".$passwdA."/".$passwdB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/fly365/includes/configure.php || 
  sed -i 's/".$passwdA."/".$passwdB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/biubiu/includes/configure.php
  ";

            Storage::disk('local')->put('shell/'.$aHeaderName.'.txt', $str);
            $namepath = 'storage/shell/'.$aHeaderName.'.txt';
            return response()->download(str_replace('\\', '/', public_path()).'/'.$namepath);
        }else{
            //不是同一台服务器
            if(is_null($passwdB)){
                return back()->with('bpwd',1);
            }
            $str = "
在".$urlA."服务器上执行\r\n
cd /www/web/".$aHeaderName."_".$aFooterName."/public_html && 
  rm -rf *.sql *.gz && 
  tar -zcvf zc.gz * .[!.]* && 
  mysqldump -u root -p".$passwdA." ".$dbnameA.">zc.sql \r\n
在".$urlB."服务器上执行\r\n
cd /www/web/".$bHeaderName."_".$bFooterName."/public_html && 
  rm -rf *.sql *.gz index.html && 
  wget http://www.".$aHeaderName.".".$aFooterName."/zc.gz && 
  wget http://www.".$aHeaderName.".".$aFooterName."/zc.sql && 
  mysql -u root -p".$passwdB." ".$dbnameB."<zc.sql && 
  tar -zxvf zc.gz && 
  rm -rf *.sql *.gz && 
  sed -i 's/".$urlA."/".$urlB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/.htaccess && 
  sed -i '/DB_DATABASE/s/".$dbnameA."/".$dbnameB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/includes/configure.php && 
  sed -i '/DB_DATABASE/s/".$dbnameA."/".$dbnameB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/whbost/includes/configure.php || 
  sed -i '/DB_DATABASE/s/".$dbnameA."/".$dbnameB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/fly365/includes/configure.php || 
  sed -i '/DB_DATABASE/s/".$dbnameA."/".$dbnameB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/biubiu/includes/configure.php && 
  sed -i 's/".$aHeaderName."/".$bHeaderName."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/includes/configure.php && 
  sed -i 's/".$aHeaderName."/".$bHeaderName."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/whbost/includes/configure.php || 
  sed -i 's/".$aHeaderName."/".$bHeaderName."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/fly365/includes/configure.php || 
  sed -i 's/".$aHeaderName."/".$bHeaderName."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/biubiu/includes/configure.php && 
  sed -i 's/".$aFooterName."/".$bFooterName."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/includes/configure.php && 
  sed -i 's/".$aFooterName."/".$bFooterName."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/whbost/includes/configure.php || 
  sed -i 's/".$aFooterName."/".$bFooterName."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/fly365/includes/configure.php || 
  sed -i 's/".$aFooterName."/".$bFooterName."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/biubiu/includes/configure.php && 
  sed -i 's/".$passwdA."/".$passwdB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/includes/configure.php && 
  sed -i 's/".$passwdA."/".$passwdB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/whbost/includes/configure.php || 
  sed -i 's/".$passwdA."/".$passwdB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/fly365/includes/configure.php || 
  sed -i 's/".$passwdA."/".$passwdB."/g' /www/web/".$bHeaderName."_".$bFooterName."/public_html/biubiu/includes/configure.php 
  ";
            Storage::disk('local')->put('shell/'.$aHeaderName.'.txt', $str);
            $namepath = 'storage/shell/'.$aHeaderName.'.txt';
            return response()->download(str_replace('\\', '/', public_path()).'/'.$namepath);
        }
    }
}
