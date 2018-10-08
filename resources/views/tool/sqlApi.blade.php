@extends('common.master')
@section('title','添加品牌')
@section('content')
    <div class="col-lg-12 contentBox" id="detail">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>网站调整价格</h3>
            </div>
            @include('common.errors')
            <div class="panel-body">
                <form action="/tool/sqlApi" method="post">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="url">网站</label>
                        <input type="text" name="url"  class="form-control" placeholder="输入网站地址">
                    </div>
                    <div class="form-group">
                        <label for="sql">输入内容</label>
                        <textarea type="text" value="{{old('sql')}}" name="sql" class="form-control" style="height: 300px">
                                </textarea>
                        <button type="submit" class="btn btn-primary">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('foot-js')

    @if(session('fail'))
        <script>
            swal('','失败!','errors')
        </script>
    @endif
    @if(session('message'))
        <script>
            swal('','成功!','success')
        </script>
    @endif
@endsection



