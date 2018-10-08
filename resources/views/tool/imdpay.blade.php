@extends('common.master')
@section('title','添加品牌')
@section('content')
    <div class="col-lg-12 contentBox" id="detail">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>同步文件</h3>
            </div>
            @include('common.errors')
            <div class="panel-body">
                <form action="/tool/imdpay" method="post">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="urls">网站</label>
                        <input type="text" name="urls"  class="form-control" placeholder="输入网站地址,用|隔开,如www.aaa.com|www.bbb.com|www.ccc.com">
                    </div>
                    <div class="form-group">
                        <label for="path">路径</label>
                        <input type="text" name="path" class="form-control" placeholder="输入要修改的文件地址,如includes/modules/payment/imdpay.php">
                    </div>
                    <div class="form-group">
                        <label for="content">输入内容</label>
                        <textarea type="text" value="{{old('content')}}" name="content" class="form-control" style="height: 600px">
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



