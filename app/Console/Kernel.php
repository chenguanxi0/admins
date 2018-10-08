<?php

namespace App\Console;

use App\MyClass\timeDeal;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\TestWeb;
use Mail;
use App\Http\Controllers\ToolController;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {




        $schedule->call(function()
        {
            $timeDeal = new timeDeal();
            $timeDeal->allDeal();
        })->daily();
        $schedule->call(function()
        {

            $urls = TestWeb::query()->where('is_test','T')->get();
            $urlStr = '';
            $urlArr = array();
            foreach ($urls as $url){
                $code = $this->curl(str_replace('http://','http://www.', $url->url).'/index.php');
                $code_1 = $this->curl(str_replace('http://','https://www.', $url->url).'/index.php');

//                $code = file_get_contents($url->url);
                if(!$code || !strpos($code,'indexHomeBody') ){
                    if (!$code_1 || !strpos($code_1,'indexHomeBody')){
						$code = $this->curl(str_replace('http://','http://www.', $url->url).'/index.php');
                        $code_1 = $this->curl(str_replace('http://','https://www.', $url->url).'/index.php');
						 if(!$code || !strpos($code,'indexHomeBody') ){
							 if (!$code_1 || !strpos($code_1,'indexHomeBody')){
								   $urlStr.= $url->url.' | ';
                                   array_push($urlArr,$url->url);
							 }
						 }
                      
                    }

                }

            }

            if($urlStr != ''){
                //$this->mail($urlStr,'网站出现问题');
                $tool = new ToolController;
                foreach ($urlArr as $k=>$v){
                    $params = array('sql' => 'REPAIR TABLE  `sessions` ,  `whos_online`');
                    $sqlApiUrl = str_replace('http://','http://www.', $v).'/sqlApi.php';
                    $res = $tool->send3rdRequest($params,$sqlApiUrl);

                }
                $okArr = '';$failArr = '';

                foreach ($urlArr as $k=>$v){
                    $code = $this->curl(str_replace('http://','http://www.', $v).'/index.php');
                    $code_1 = $this->curl(str_replace('http://','https://www.', $v).'/index.php');

//                $code = file_get_contents($url->url);
                    if(!$code || !strpos($code,'indexHomeBody') ){
                        if (!$code_1 || !strpos($code_1,'indexHomeBody')){
                            $code = $this->curl(str_replace('http://','http://www.', $v).'/index.php');
                            $code_1 = $this->curl(str_replace('http://','https://www.', $v).'/index.php');
                            if(!$code || !strpos($code,'indexHomeBody') ){
                                if (!$code_1 || !strpos($code_1,'indexHomeBody')){
                                    $failArr .= $v.' | ';
                                }
                            }

                        }

                    }

                }

                if ($failArr != ''){
                    $this->mail($failArr,' 网站打不开，请检查！');
                }
            }

        })->everyTenMinutes();
    }
    public function mail($url,$text)
    {
        Mail::raw($url.$text, function ($message) use ($text) {
            $to = '491788533@qq.com';
            $to_1 = '304739233@qq.com';
            $to_2 = 'hello2018year@foxmail.com';
            $message ->to($to)->subject($text)->cc($to_1)->cc($to_2);
//            $message ->to($to)->subject($text);

        });
    }
    public function mailMe($text)
    {
        Mail::raw($text, function ($message) use ($text) {
            $to = '491788533@qq.com';
            $message ->to($to)->subject($text);
            //->cc($to_1)->cc($to_2)
        });
    }
    public function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        return $data;
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
