@extends('common.master')
@section('title','数据库备份')
<style>
    .dbDebug{display: none}
    .red{color: red}
</style>
@section('content')
    <br>
    <br>
    <a href="{{$filename}}" class="btn btn-primary">
        备份数据库
    </a>
    <br>
    <br>
    <h3>如需恢复数据请自行使用命令行工具操作</h3>
    <p>1.mysql -u root -p<span class="red">123456</span>;   --进入数据库</p>
    <p>2.drop database <span class="red">admin</span>;   --删除数据库</p>
    <p>3.create database <span class="red">admin</span>;   --创建数据库</p>
    <p>4.use <span class="red">admin</span>   --选择数据库</p>
    <p>5.source <span class="red">C:\Users\Administrator\Desktop\20171227174128_all_v1.sql</span>  --导入数据</p>
    <h2 class="red">红色标记字段根据实际情况填写</h2>
@stop