@extends('common.master')
@section('title','添加分类')
@section('content')
    <div class="col-lg-12 contentBox" id="detail">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>添加分类</h3>
            </div>
            @include('common.errors')
            <div class="panel-body">
                <h2 style="text-align: center">添加分类</h2>
                <form action="/categorys/add" method="post" class="form-horizontal">
                    {{csrf_field()}}
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
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">分类名称:</label>
                        <div class="col-sm-8">
                            <input type="text" name="name" class="form-control" id="name">区分大小写
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="parent_catrgory" class="col-sm-2 control-label">父分类名称:</label>
                        <div class="col-sm-8">
                            <input type="text" name="parent_catrgory" class="form-control" id="parent_catrgory">(区分大小写 || 一级分类则不填)
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

                    <div class="form-group" style="text-align: center">
                        <button type="submit" class="btn btn-success">添加</button>
                    </div>
                </form>

            </div>


        </div>
    </div>
@stop

@section('foot-js')
    @if(session('fail'))
        <script>
            swal('请确认','该父分类不存在!','error')
        </script>
    @endif
    @if(session('haveL'))
        <script>
            swal('语言重复','请重新选择语言!','error')
        </script>
    @endif
    @if(session('success'))
        <script>
            swal('','分类添加成功!','success')
        </script>
    @endif
@endsection
