<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/30
 * Time: 16:41
 */
namespace App\MyClass;
use App\Product_description;
use Illuminate\Support\Facades\DB;
class timeDeal
{
    public function allDeal()
    {
        //默认每个产品取5条评论，间隔5天
        //首先判断是否有5条评论，如果没有就直接取所有评论，如果有则从最后面取5条
        $product_descriptions = Product_description::all();
        //2.找出每一条产品的所有评论
        foreach ($product_descriptions as $k=>$product_description){
            $modelAllCommits[$k] = $product_description->getCommits;//该moldel所有评论
            $count = count($modelAllCommits[$k]);   //实际数量
            $num = $product_description->isProduct->commitsNum; //指定条数
            $days = $product_description->isProduct->days;  //间隔天数

            if ($count >= $num) {//判断是否有这么多条评论
                //如果满足条件 则选指定条数的评论出来分配时间
                $modelShowCommits = $modelAllCommits[$k]->sortByDesc('id')->take($num);

                for ($i = 0; $i < $num; $i++) {
                    $newCreated[$i] = date('Y-m-d H:i:s', rand(time() - $days * 86400, time()));
                }
                rsort($newCreated);
                foreach ($modelShowCommits->values() as $kk => $modelShowCommit) {
                    $arr[$kk]['id'] = $modelShowCommit->id;
                    $arr[$kk]['created_at'] = $newCreated[$kk];
                    $arr[$kk]['updated_at'] = $newCreated[$kk];
                    $arr[$kk]['replyTime'] = date('Y-m-d H:i:s', strtotime($newCreated[$kk]) + rand(0, 2) * 86400 + rand(0, 86400));;
                }
                $this->updateBatch('commits',$arr);
            }elseif($count > 0 ){
                $modelShowCommits = $modelAllCommits[$k]->sortByDesc('id')->take($count);
                for ($i = 0; $i < $count; $i++) {
                    $newCreated[$i] = date('Y-m-d H:i:s', rand(time() - $days * 86400, time()));
                }
                rsort($newCreated);
                foreach ($modelShowCommits->values() as $kk => $modelShowCommit) {
                    $arr[$kk]['id'] = $modelShowCommit->id;
                    $arr[$kk]['created_at'] = $newCreated[$kk];
                    $arr[$kk]['updated_at'] = $newCreated[$kk];
                    $arr[$kk]['replyTime'] = date('Y-m-d H:i:s', strtotime($newCreated[$kk]) + rand(0, 2) * 86400 + rand(0, 86400));;
//                    $modelShowCommit->created_at = $newCreated[$kk];
//                    $modelShowCommit->replyTime = date('Y-m-d H:i:s', strtotime($newCreated[$kk]) + rand(0, 2) * 86400 + rand(0, 86400));
                }
                $this->updateBatch('commits',$arr);
            }


        }
    }
    public function updateBatch($tableName = "", $multipleData = array()){

        if( $tableName && !empty($multipleData) ) {

            // column or fields to update
            $updateColumn = array_keys($multipleData[0]);
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
}