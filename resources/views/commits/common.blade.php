@extends('common.master')
@section('title','通用评论')
@section('content')
    <div class="col-lg-12 contentBox" id="list">
        <div class="panel panel-default">

            <div class="panel-heading">
                <h3>通用评论列表</h3>


                <br class="clearBoth">
            </div>

        @include('common.errors')
        <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>品牌</th>
                            <th>语言</th>
                            <th>用户名</th>
                            <th>评论内容</th>
                            <th>时间</th>
                            <th>回复内容</th>
                            <th>回复时间</th>
                            <th>星级</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($commonCommits as $k=>$commonCommit)
                            <tr>
                                <td>{{$commonCommit->getBrand->name}}</td>
                                <td>{{$commonCommit->getLan->code}}</td>
                                <td>{{$commonCommit->username}}</td>
                                <td>{{str_limit($commonCommit->content,40,'...')}}</td>
                                <td>
                                    {{$commonCommit->created_at}}
                                </td>
                                <td>{{$commonCommit->reply}}</td>
                                <td>{{$commonCommit->replyTime}}</td>
                                <td>{{$commonCommit->star}}</td>
                                <td>
                                    <a href="/commits/{{$commonCommit->id}}/delete">
                                        <span class="fa fa-trash-o fa-lg text-danger"></span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{$commonCommits->links()}}
                </div>

            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
@endsection

@section('foot-js')

    @if(session('add'))
        <script>
            swal('','评论添加成功!','success')
        </script>
    @endif
    @if(session('success'))
        <script>
            swal('','删除成功!','success')
        </script>
    @endif

@endsection