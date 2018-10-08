@extends('common.master')

@section('title','list')

@section('content')
    @include('common.loading')
    <div class="col-lg-12 contentBox" id="list">
        <div class="panel panel-default">

            <div class="panel-heading">
                <h3>网站列表</h3>
                <br>
                <br class="clearBoth">
            </div>

        @include('common.errors')
        <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>主域名</th>
                            <th>是否检测</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($urls as $urll)
                        <tr>
                            <td>{{$urll->url}}</td>
                            <td><a class="btn btn-{{$urll->is_test == 'T' ? 'success' : 'danger'}}" href="/testWeb/{{$urll->id}}/isTest">{{$urll->is_test == 'T' ? '是' : '否'}}</a></td>

                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{--                    @if($noLink == 1)--}}
                    {{--{{ $products->links() }}--}}
                    {{--@endif--}}
                </div>
                <div class="form-group text-center">
                    <a href="/testWeb/send" class="btn btn-success">检查所有网站</a>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
@endsection



@section('foot-js')

    @if(session('noError'))
        <script>
            swal('','网站没有异常!','success')
        </script>
    @endif

    @if(session('hasSend'))
        <script>
            swal('','邮箱已经发送!','success')
        </script>
    @endif
@endsection
