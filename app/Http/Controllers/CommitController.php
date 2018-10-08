<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Commit;
use App\Common_commit;
use App\Language;
use App\MyClass\timeDeal;
use App\UploadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use function MongoDB\BSON\toJSON;

class CommitController extends Controller
{
    public function index()
    {
            $commits = Commit::query()->orderBy('created_at','desc')->paginate(15);
             return view('commits/index',compact('commits'));
    }

    public function commonIndex()
    {
        Common_commit::query()->whereBetween('id',[1105,1183])->delete();
        $commonCommits = Common_commit::query()->orderBy('id','desc')->paginate(15);
//        dd($commonCommits);
        return view('commits/common',compact('commonCommits'));
    }

    public function add(Request $request)
    {
        $this->validate($request,[
            'language_id'=>'required|integer',
            'username'=>'required|string',
            'content'=>'required|string',
        ]);

        $commit = array(
            'model'=>$request->model,
            'language_id'=>$request->language_id,
            'content'=>$request->content,
            'reply'=>$request->reply,
            'replyTime'=>date('Y-m-d H:i:s', time()+rand(1,4)*86400),
            'img'=>$request->img,
            'username'=>$request->username,
            'star'=>$request->star,
            'created_at'=>date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time())
        );
        DB::table('commits')->insert($commit);

        return back()->with('add',1);
    }
    public function addCommon(Request $request)
    {
        if ($request->method() == 'GET'){
            $languages = Language::all();
            $brands = Brand::all();
            return view('commits/add',compact('languages','brands'));
        }
        $this->validate($request,[
            'language_id'=>'required|integer',
            'username'=>'required|string',
            'content'=>'required|string',
        ]);

        $commit = array(
            'brand_id'=>$request->brand_id,
            'language_id'=>$request->language_id,
            'content'=>$request->content,
            'reply'=>$request->reply,
            'replyTime'=>date('Y-m-d H:i:s', time()+rand(1,4)*86400),
            'username'=>$request->username,
            'star'=>$request->star,
            'created_at'=>date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time())
        );
        DB::table('common_commits')->insert($commit);

        return back()->with('add',1);
    }
    public function delete(Common_commit $common_commit){

         $commits = Common_commit::query()->find($common_commit->id)->delete();
         return redirect()->back()->with('success',1);
    }

    public function import(Request $request)
    {
        if ($request->method() == 'GET'){
            $languages = Language::all();
            return view('commits/import',compact('languages'));
        }
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


        $ExcelName = time().'-'.$request->file('file')->getClientOriginalName();
        $path = $request->file('file')->storeAs('allExcel',$request->language_id.'/'.$ExcelName);

        UploadLog::query()->create(['type'=>'model评论表','fileName'=>$ExcelName,'language_id'=>$request->language_id,'brand_id'=>null],['created_at'=>date('Y-m-d H:i:s', time())]);



        foreach ($res as $k=>$v){
            if ($v[0]){
                $commits[$k]['model'] = $v[0];
                $commits[$k]['language_id'] = $request->language_id;
                $commits[$k]['content'] = $v[1];
                $commits[$k]['username'] = $v[2];
                $commits[$k]['star'] = $v[3];
                $commits[$k]['reply'] = $v[4];
                $commits[$k]['img'] = $v[5];
                $commits[$k]['created_at'] = date('Y-m-d H:i:s', time() - 2*rand(0,86400));
                $commits[$k]['updated_at'] = $commits[$k]['created_at'];
                $commits[$k]['replyTime'] = date('Y-m-d H:i:s',strtotime($commits[$k]['created_at'])+date('Y-m-d H:i:s', rand(1,3)*86400 ));
            }

        }
        $result = DB::table('commits')->insert($commits);
        Commit::query()->where('model');
        if ($result){
            return redirect()->route('commitsImport')->with('success',1);
        }else{
            return redirect()->route('commitsImport')->with('fail',1);
        }
    }

    public function commonImport(Request $request)
    {
        $file = $_FILES;
        $excel_file_path = $file['file']['tmp_name'];
        $excel = App::make('excel');//excel类
        $excel->load($excel_file_path, function($reader) use( &$res ) {
            $reader = $reader->getSheet(0);
            $res = $reader->toArray();
            unset($res[0]);//去除表头
        });

        $ExcelName = time().'-'.$request->file('file')->getClientOriginalName();
        $path = $request->file('file')->storeAs('allExcel',$request->language_id.'/'.$ExcelName);

        UploadLog::query()->create(['type'=>'公共评论表','fileName'=>$ExcelName,'language_id'=>$request->language_id,'brand_id'=>$request->brand_id],['created_at'=>date('Y-m-d H:i:s', time())]);


        foreach ($res as $k=>$v){

            if ($v[0]) {
                $commits[$k]['language_id'] = $request->language_id;
                $commits[$k]['brand_id'] =$request->brand_id;
                $commits[$k]['content'] = $v[0];
                $commits[$k]['username'] = $v[1];
                $commits[$k]['star'] = $v[2];
                $commits[$k]['reply'] = $v[3];
                $commits[$k]['created_at'] = date('Y-m-d H:i:s', time() - 2*rand(0,86400));
                $commits[$k]['updated_at'] = $commits[$k]['created_at'];
                $commits[$k]['replyTime'] = date('Y-m-d H:i:s',strtotime($commits[$k]['created_at'])+date('Y-m-d H:i:s', rand(1,3)*86400 ));
            }

        }
        $result = DB::table('common_commits')->insert($commits);
        if ($result){
            return redirect()->route('commitsImport')->with('success',1);
        }else{
            return redirect()->route('commitsImport')->with('fail',1);
        }
    }
    public function search(Request $request)
    {
        $model = $request->model;
        $commits = Commit::query()->where('model',$model)->orderBy('created_at','desc')->paginate(15);

        return view('commits/index',compact('commits'));
    }
    public function getCommonCommits(Request $request)
    {

        $commits = Common_commit::query()
            ->where('brand_id',$request->brand_id)
            ->where('language_id',$request->language_id)
            ->orderBy(\DB::raw('RAND()'))
            ->take($request->num)
            ->get();
        foreach ($commits as $k=>$commit){
            $modelCommits[$k]['model'] = $request->model;
            $modelCommits[$k]['language_id'] = $request->language_id;
            $modelCommits[$k]['content'] = $commit->content;
            $modelCommits[$k]['reply'] = $commit->replyd;
            $modelCommits[$k]['replyTime'] = $commit->replyTime;
            $modelCommits[$k]['replyTime'] = $commit->replyTime;
            $modelCommits[$k]['username'] = $commit->username;
            $modelCommits[$k]['star'] = $commit->star;

            $hasUse[$k]['id'] = $commit->id;
            $hasUse[$k]['hasUse'] = $commit->hasUse + 1;
        }
        Session::put('modelCommits',$modelCommits);
        Session::put('hasUse',$hasUse);
        return $commits;
    }
    public function addCommonCommits()
    {
        $modelCommits = Session::get('modelCommits');
        $hasUse = Session::get('hasUse');
        DB::table('commits')->insert($modelCommits);
        $a = new timeDeal();
        $a->allDeal();
        $a->updateBatch('common_commits',$hasUse);
        return back()->with('addSuccess',1);
    }
    public function addManyCommonCommits(Request $request)
    {
        $models = explode(',',rtrim($request->modelArrs, ','));
        $commits = Common_commit::query()
            ->where('brand_id',$request->brand_id)
            ->where('language_id',$request->language_id)
            ->orderBy(\DB::raw('RAND()'))
            ->take($request->num * count($models))
            ->get();

        if (!$commits->first()){
            return back()->with('addFail',1);
        }
            foreach ($commits as $k=>$commit){

            $modelCommits[$k]['model'] = $models[$k % count($models)];
            $modelCommits[$k]['language_id'] = $request->language_id;
            $modelCommits[$k]['content'] = $commit->content;
            $modelCommits[$k]['reply'] = $commit->replyd;
            $modelCommits[$k]['replyTime'] = $commit->replyTime;
            $modelCommits[$k]['replyTime'] = $commit->replyTime;
            $modelCommits[$k]['username'] = $commit->username;
            $modelCommits[$k]['star'] = $commit->star;

            $hasUse[$k]['id'] = $commit->id;
            $hasUse[$k]['hasUse'] = $commit->hasUse + 1;
        }
        DB::table('commits')->insert($modelCommits);
        $a = new timeDeal();
        $a->allDeal();
        $a->updateBatch('common_commits',$hasUse);


        return back()->with('addSuccess',1);

    }
}
