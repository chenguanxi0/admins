@extends('common.master')
@section('title','添加属性')
@section('content')
    <div class="col-lg-12 contentBox" id="detail">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>添加属性</h3>
            </div>
            @include('common.errors')
            <div class="panel-body">



                <form action="/tool/optionsAdd" method="post" class="form-horizontal" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="web" class="col-sm-2 control-label">网站:</label>
                        <div class="col-sm-8">
                            <input type="text" name="web" class="form-control" id="web">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="optionName" class="col-sm-2 control-label">属性名称:</label>
                        <div class="col-sm-8">
                            <input type="text" name="optionName" class="form-control" id="optionName">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="language" class="col-sm-2 control-label">语言id:</label>
                        <div class="col-sm-8">
                            <input type="text" name="language" class="form-control" id="language" placeholder="英语一般为1,其他语言在数据库中查找">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fileId1" class="col-sm-2 control-label">上传表格:</label>
                        <div class="col-sm-8">
                            <input id="fileId1" type="file" class="form-control" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" name="file"/>
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

    @if(session('success'))
        <script>
            swal('','语言添加成功!','success')
        </script>
    @endif
@endsection