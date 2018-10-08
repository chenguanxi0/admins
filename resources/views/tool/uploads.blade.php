@extends('common.master')
@section('title','uploads')
@section('content')
    <form action="{{url('tool/uploads')}}" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        <input type="file" name="uploadfile"/>
        <button type="submit" class="btn btn-primary">上传</button>
    </form>
@stop