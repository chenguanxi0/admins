@extends('common.master')
@section('title','上传评论')
@section('content')
    <div class="col-lg-12 contentBox" id="detail">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>产品评论上传</h3>
            </div>
            @include('common.errors')
            <div class="panel-body">
                <form action="/commits/import" method='post' class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}
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
                            <i class="fa fa-btn fa-sign-in"></i> 确认上传
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>通用评论上传</h3>
            </div>
            @include('common.errors')
            <div class="panel-body">
                <form action="/commits/import/common" method='post' class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}
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
                        <label for="brand_id" class="col-sm-2 control-label">品牌:</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="brand_id">
                                <option value="0">--请选择--</option>
                                @foreach(\App\Brand::all() as $brand)
                                    <option value="{{$brand->id}}">{{$brand->name}}</option>
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
                            <i class="fa fa-btn fa-sign-in"></i> 确认上传
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
            swal('','评论上传成功!','success')
        </script>
    @endif
    @if(session('fail'))
        <script>
            swal('','评论上传失败!','success')
        </script>
    @endif
@endsection
