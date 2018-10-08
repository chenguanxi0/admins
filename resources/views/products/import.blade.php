@extends('common.master')
@section('title','上传产品')
@section('content')
    <div class="col-lg-12 contentBox" id="detail">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>上传新产品</h3>
            </div>
            @include('common.errors')
            <div class="panel-body">
                <form action="/products/testResult" method='post' class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="importType" value="1">
                    <div class="form-group">
                        <label for="brand_id" class="col-sm-2 control-label">品牌:</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="brand_id">
                                <option value="0">--请选择--</option>
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
                                <option value="0">--请选择--</option>
                            @foreach($languages as $language)
                                    <option value="{{$language->id}}">{{$language->code}}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fileId1" class="col-sm-2 control-label"></label>
                        <div class="col-sm-8">
                            <input id="fileId1" type="file" class="form-control" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" name="file"/>
                        </div>
                    </div>
                    <div class="form-group" style="text-align: center">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-btn fa-sign-in"></i> 校验表格
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>上传已有产品其他语言</h3>
            </div>
            @include('common.errors')
            <div class="panel-body">
                <form action="/products/testResult" method='post' class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="importType" value="2">
                    <div class="form-group">
                        <label for="brand_id" class="col-sm-2 control-label">品牌:</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="brand_id">
                                <option value="0">--请选择--</option>
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
                                <option value="0">--请选择--</option>
                                @foreach($languages as $language)
                                    <option value="{{$language->id}}">{{$language->code}}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fileId1" class="col-sm-2 control-label"></label>
                        <div class="col-sm-8">
                            <input id="fileId1" type="file" class="form-control" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" name="file"/>
                        </div>
                    </div>
                    <div class="form-group" style="text-align: center">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-btn fa-sign-in"></i> 校验表格
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop

@section('foot-js')
    @if(session('success'))
        <script>
            swal('','数据上传成功!','success')
        </script>
    @endif
    @if(session('cateIsNull'))
        <script>
            swal('一级分类不能为空','请检查表中分类','error')
        </script>
    @endif
@endsection
