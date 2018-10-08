@extends('common.master')
@section('title','上传结果')

@section('content')
    <div class="col-lg-12 contentBox" id="list">
        <div class="panel panel-default">

            <div class="panel-heading">
                <h3>上传表格校验结果</h3>
                <br class="clearBoth">
            </div>

        @include('common.errors')
        <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>model</th>
                            <th>产品名称</th>
                            <th>现价(美元)</th>
                            <th>一级分类</th>
                            <th>二级分类</th>
                            <th>三级分类</th>
                            <th>四级分类</th>
                            <th>model是否重复</th>
                            <th>语言是否重复</th>
                            <th>该model的该分类是否重复</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>


                        @foreach($res as $result)

                            <tr>
                                <td>{{$result[0]}}</td>
                                <td>{{$result[1]}}</td>
                                <td>{{$result[4]}}</td>
                                <td>{{$result[5]}}</td>
                                <td>{{$result[6]}}</td>
                                <td>{{$result[7]}}</td>
                                <td>{{$result[8]}}</td>
                                <td>
                                    @if($result[11] == 0)
                                    <button class="btn btn-success">否</button>
                                    @else
                                    <button class="btn btn-danger">是</button>
                                    @endif
                                </td>
                                <td>
                                    @if($result[12] == 0)
                                        <button class="btn btn-success">否</button>
                                    @else
                                        <button class="btn btn-danger">是</button>
                                    @endif</td>
                                <td>
                                    @if($result[13] == 0)
                                        <button class="btn btn-success">否</button>
                                    @else
                                        <button class="btn btn-danger">是</button>
                                    @endif
                                </td>
                                <td>
                                    @if($result[14] == 0)
                                        <button class="btn btn-danger">更新</button>
                                    @else
                                        <button class="btn btn-success">添加</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach


                        </tbody>
                    </table>
                    <form action="/products/import2" method="post">
                        <input type="hidden" name="brand_id" value="{{$brand_id}}">
                        <input type="hidden" name="language_id" value="{{$language_id}}">
                        {{csrf_field()}}
                        <div style="text-align: center">
                            <button class="btn btn-info">确认上传</button>
                            <br>
                        </div>
                    </form>
                </div>

            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
@endsection