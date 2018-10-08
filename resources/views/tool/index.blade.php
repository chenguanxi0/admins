@extends('common.master')
@section('title','tool')
@section('content')
<div class="col-xs-2">
    <a href="{{url('tool/uploads')}}" class="btn btn-info">上传图片</a>
</div>

<div class="col-xs-2">
    <a href="{{url('tool/category')}}" class="btn btn-info">分类</a>
</div>
@stop