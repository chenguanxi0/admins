@extends('common.master')
@section('title','新建网站')
@section('content')
    <div class="col-lg-12 contentBox" id="detail">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>新建网站</h3>
            </div>
        @include('common.errors')
        <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="">
                    <form method="post" action="/webs/add" class="form-horizontal">
                        {{csrf_field()}}

                        <div class="form-group">
                            <label for="url" class="col-sm-2 control-label" >网站地址:</label>
                            <div class="col-sm-8">
                                <input type="text" name="url" class="form-control" id="url" value="" placeholder="输入不带'www'的域名">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="type" class="col-sm-2 control-label">网站类型:</label>
                            <div class="col-sm-8">
                                <select name="type" class="form-control">
                                    <option value="zc">zc</option>
                                    <option value="op">op</option>
                                    <option value="ot">ot</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="brand_id" class="col-sm-2 control-label">品牌:</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="brand_id">
                                    @foreach($brands as $brand)
                                        <option value="{{$brand->id}}">{{$brand->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="langugae_id" class="col-sm-2 control-label">语言:</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="language_id">
                                    @foreach($languages as $language)
                                        <option value="{{$language->id}}">{{$language->code}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="form-group" style="text-align: center">
                            <button type="submit" class="btn btn-success">提交</button>
                        </div>

                    </form>
                </div>

            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
@endsection