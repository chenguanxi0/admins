@extends('common.master')
@section('title','上传记录')
@section('content')
    <div class="col-lg-12 contentBox" id="list">
        <div class="panel panel-default">

            <div class="panel-heading">
                <h3>上传记录</h3>
                <br class="clearBoth">
            </div>

        <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>语言</th>
                            <th>类型</th>
                            <th>文件名</th>
                            <th>上传时间</th>
                            <th>下载</th>
                            <th>删除记录</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($uploadLogs as $uploadLog)
                        <tr>
                            <td>{{$uploadLog->getLan->code}}</td>
                            <td>{{$uploadLog->type}}</td>
                            <td>{{$uploadLog->getbrand->name or ''}} {{$uploadLog->fileName}}</td>
                            <td>{{$uploadLog->created_at}}</td>
                            <td><a href="/storage/allExcel/{{$uploadLog->language_id}}/{{$uploadLog->fileName}}" class="btn"><i class="fa fa-download fa-fw "></i></a></td>
                            <td><a href="/tool/log/{{$uploadLog->id}}/delete"><i class="fa fa-trash-o fa-lg"></i></a></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>

            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
@endsection

@section('foot-js')
    @if(session('delete'))
        <script>
            swal('','删除成功!','success')
        </script>
    @endif
@endsection