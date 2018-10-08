<?php

namespace App\Http\Controllers;

use App\Commit;
use Illuminate\Http\Request;
use Mail;
use Illuminate\Support\Facades\DB;
use App\TestWeb;
use App\Http\Controllers\ToolController;


class TestWebController extends Controller
{
    public function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        $data = curl_exec($ch);
        return $data;
    }
    public function send()
    {
        $urls = TestWeb::query()->where('is_test','T')->get();
        $urlStr = '';
        $urlArr = array();
        foreach ($urls as $url){
            $code = $this->curl(str_replace('http://','http://www.', $url->url).'/index.php');
            if($code && !strpos($code,'indexHomeBody')){
                $urlStr.= $url->url.' | ';
                array_push($urlArr,$url->url);
            }
        }
        if($urlStr == ''){
            return back()->with('noError',1);
        }
        $this->mail($urlStr);
        $tool = new ToolController;
        $okArr = '';
        $failArr = '';
        foreach ($urlArr as $k=>$v){
            $params = array('sql' => 'REPAIR TABLE  `sessions` ,  `whos_online`');
            $sqlApiUrl = str_replace('http://','http://www.', $v).'/sqlApi.php';
            $res = $tool->send3rdRequest($params,$sqlApiUrl);
        }
        $okArr = '';$failArr = '';
        foreach ($urlArr as $k=>$v){
            $code_2 = file_get_contents($v);
            if(!strpos($code_2,'indexHomeBody')){
                $failArr .= $v.' | ';
            }else{
                $okArr .= $v.' | ';
            }
        }
        if ($okArr != ''){
            $this->mail($okArr,'网站已经修复完成！');
        }
        if ($failArr != ''){
            $this->mail($failArr,' 网站修复失败,请手动修复！');
        }

        return back()->with('hasSend',1);
    }
    public function mail($url,$text)
    {
        Mail::raw($url.$text, function ($message) use ($text) {
            $to = '491788533@qq.com';
            $to_1 = '304739233@qq.com';
            $to_2 = 'hello2018year@foxmail.com';
            $message ->to($to)->subject($text)->cc($to_1)->cc($to_2);

        });
    }
    public function index()
    {
        $urls = TestWeb::all();

        return view('testWeb/list',compact('urls'));

    }
    public function isTest($testWeb_id)
    {
        $web = TestWeb::query()->find($testWeb_id);
        if ($web->is_test == 'T'){
            $web->is_test = 'F';
        }else{
            $web->is_test = 'T';
        }
        $web->save();
        return back();
    }
    public function add(Request $request)
    {
        if ($request->method() == 'GET'){
            return view('testWeb/add');
        }
        $urls = $request->url;
        $data = array();
        if (strpos($urls,'|')){
            $urls = explode('|',$urls);
            foreach ($urls as $k=>$v){
                $data[$k]['url'] = $v;
            }
        }else{
            $data = ['url'=>$urls];
        }

        $res = DB::table('testWeb')->insert($data);
        return back();
    }

    public function mailFail($url)
    {
        Mail::raw($url.' 网站修复失败！', function ($message) {
            $to = '491788533@qq.com';
            $to_1 = '304739233@qq.com';
            $to_2 = 'hello2018year@foxmail.com';
            $message ->to($to)->subject('网站修复失败,请手动修复')->cc('304739233@qq.com')->cc('hello2018year@foxmail.com');
            //->cc('304739233@qq.com')->cc('hello2018year@foxmail.com')
        });
    }
    public function mailOk($url)
    {
        Mail::raw($url.' 网站已经修复完成！', function ($message) {
            $to = '491788533@qq.com';
            $to_1 = '304739233@qq.com';
            $to_2 = 'hello2018year@foxmail.com';
            $message ->to($to)->subject('网站已经修复完成')->cc('304739233@qq.com')->cc('hello2018year@foxmail.com');
            //->cc('304739233@qq.com')->cc('hello2018year@foxmail.com')
        });
    }
    public function api($action,$md5,$urls)
    {

        if (strtolower($md5) != strtolower(md5($urls.'BC5E3ED3-DE70-44b1-B55C-ED31FC5FF368'))){
            $msg = 'failed';
            return $msg;
        }

        $data = array();
        if (strpos($urls,'|')){
            $urls = explode('|',$urls);
            foreach ($urls as $k=>$v){
                $data[$k]['url'] = 'http://'.$v;
            }
        }else{
            $data = ['url'=>'http://'.$urls];
        }
        if ($action == 1){
            //添加操作
            foreach ($data as $k=>$v){

                $isUrl = testWeb::query()->where('url',$v)->get()->first();
                if ($isUrl){
                    unset($data[$k]);
                    $isUrl->is_test = 'T';
                    $res = $isUrl->save();
                    if (!$res){
                        $msg = 'failed';
                        return $msg;
                    }
                }else{
                    $res = DB::table('testWeb')->insert($data);
                    if (!$res){
                        $msg = 'failed';
                        return $msg;
                    }
                }
            }

            $msg = 'success';
            return $msg;

        }else{
            //取消监控操作
            foreach ($data as $v){
                $isUrl = testWeb::query()->where('url',$v)->get()->first();
                if ($isUrl){
                    //网站已经存在
                    $isUrl->is_test = 'F';
                    $res = $isUrl->save();
                    if (!$res){
                        $msg = 'failed';
                        return $msg;
                    }
                }else{
                    //网站不存在 直接添加
                    $res = DB::table('testWeb')->insert(['url'=>$v,'is_test'=>'F']);
                    if (!$res){
                        $msg = 'failed';
                        return $msg;
                    }
                }
            }
            $msg = 'success';
            return $msg;
        }

    }
}
