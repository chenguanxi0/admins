@extends('common.master')
@section('title','生成shell')
@section('content')
    <div class="col-lg-12 contentBox" id="detail">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>生成shell(aaa.com->bbb.com)</h3>
            </div>
            @include('common.errors')
            <div class="panel-body">
                <form action="/tool/webShell" method="post">
                    {{csrf_field()}}
                    <label for="name">两个网站是否为同一台服务器</label>
                    <br>
                    <div>
                        <label class="radio-inline">
                            <input type="radio" name="same"  class="form-control" value="1" style="height:12px;width: 24px;" checked>是
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="same"  class="form-control" value="0" style="height:12px;width: 24px;">否
                        </label>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="urlA">源网站</label>
                        <input type="text" name="urlA"  class="form-control" placeholder="aaa.com">
                    </div>
                    <div class="form-group">
                        <label for="urlB">目标网站</label>
                        <input type="text" name="urlB"  class="form-control" placeholder="bbb.com">
                    </div>
                    <div class="form-group">
                        <label for="passwdA">aaa数据库密码</label>
                        <input type="text" name="passwdA"  class="form-control" placeholder="xxxxx">
                    </div>
                    <div class="form-group">
                        <label for="passwdB">bbb数据库密码(选填)</label>
                        <input type="text" name="passwdB"  class="form-control" placeholder="同一台服务器则不填写">
                    </div>
                    <div class="form-group">
                        <label for="dbnameA">aaa数据库名(选填)</label>
                        <input type="text" name="dbnameA"  class="form-control" placeholder="默认为aaa,可以不用填写">
                    </div>
                    <div class="form-group">
                        <label for="dbnameB">bbb数据库名(选填)</label>
                        <input type="text" name="dbnameB"  class="form-control" placeholder="默认为bbb,可以不用填写">
                    </div>
                    <button type="submit" class="btn btn-success">提交</button>
                </form>
            </div>
        </div>
    </div>
@stop

@section('foot-js')

    @if(session('bpwd'))
        <script>
            swal('','bbb服务器密码未填!','error')
        </script>
    @endif
@endsection



