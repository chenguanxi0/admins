@extends('common.master')
@section('title','新建网站')
@section('content')
    <div class="col-lg-12 contentBox" id="detail">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>添加网站</h3>
            </div>
        @include('common.errors')
        <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="">
                    <form method="post" action="/testWeb/add" class="form-horizontal">
                        {{csrf_field()}}

                        <div class="form-group">
                            <label for="url" class="col-sm-2 control-label" >网站地址:</label>
                            <div class="col-sm-8">
                                <input type="text" name="url" class="form-control" id="url" value="" placeholder="输入完整域名(带http),一个以上用 | 隔开">
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