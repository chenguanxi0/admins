@extends('common.master')
@section('title','readme')
@section('content')
    <style>
        .panel-body h3{
            color:red;
        }
    </style>
    <div class="panel panel-default">
        <div class="panel-heading">
            <b>上传数据表格式说明以及要求</b>
        </div>
        <div class="panel-body">

            <div>
                <h3>1.上传数据表必须为zencart的数据表,且需要在zc的数据表中新加入一列字段(第二条中有详细说明)</h3>
                <h3>2.对应第一条,上传的数据表需要在第一列前添加一列 v_language_id 字段  <a target="_blank" href="/storage/readme/readme_1.png" alt="" >点击查看</a></h3>
                <h3>3.同一张数据表中只能出现一种语言，即一次只能上传一种语言,如需上传同产品的其他语言需要另外整理后上传</h3>
                <div><p>&nbsp;&nbsp;&nbsp;&nbsp;详细说明:假如现在需要上传一个数据表 shoes.csv,表中同时存在英语和法语的产品,那么你需要筛选出所有的英文产品和法语产品,分为两张表 shoes_1.csv(英文) shoes_2.csv(法语),以次上传,以此类推</p></div>
                <h3>4.数据表中的model字段必须为纯数字</h3>
                <h3>5.数据表中不能有空行也不能出现中文</h3>
                <br>
                <h2>请确保需要上传的数据表都满足以上条件,再去导入数据</h2>
            </div>
            <br>

        </div>
        <!-- /.panel-body -->
    </div>

@stop