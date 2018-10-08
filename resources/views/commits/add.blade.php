@extends('common.master')
@section('title','添加评论')
@section('content')
    <div class="col-lg-12 contentBox" id="detail">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>添加公共评论</h3>
            </div>
            @include('common.errors')
            <div class="panel-body">

                <form action="/commits/addCommon" method="post" class="form-horizontal">
                    {{csrf_field()}}

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
                    <div class="form-group">
                        <label for="star" class="col-sm-2 control-label">评分:</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="star">
                                <option value="5">5星</option>
                                <option value="4">4星</option>
                            </select>

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="username" class="col-sm-2 control-label">用户名称:</label>
                        <div class="col-sm-8">
                            <input type="text" name="username" class="form-control" id="username" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="content" class="col-sm-2 control-label">评论内容:</label>
                        <div class="col-sm-8">
                            <textarea name="content" id="content" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reply" class="col-sm-2 control-label">回复内容:</label>
                        <div class="col-sm-8">
                            <textarea name="reply" id="reply" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="form-group" style="text-align: center">
                    <button type="submit" class="btn btn-success">添加</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
@section('foot-js')

    @if(session('add'))
        <script>
            swal('','添加成功!','success')
        </script>
    @endif
@endsection