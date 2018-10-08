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
                            <th><input type="checkbox" id="clickAll" onclick="checkall(this.checked)"></th>
                            <th>主域名</th>
                            <th>类型</th>
                            <th>语言</th>
                            <th>品牌</th>
                            <th>状态</th>
                            <th>创建时间</th>
                            <th>价格调整</th>
                            <th>配置网站</th>
                            <th>操作</th>
                            <th>操作</th>
                            <th>表格</th>
                            <th>图片压缩包</th>
                            <th>网站价格调整</th>
                            <th>日志</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($webs as $web)
                            <tr>
                                <th><input type="checkbox" name="checkProduct" value="{{$web->id}}"></th>
                                <td>{{$web->url}}</td>
                                <td>{{$web->type}}</td>
                                <td>{{$web->getLan->code}}</td>
                                <td>{{$web->getBrand->name}}</td>
                                <td>
                                    @if($web->hasSet == 0)
                                        <button class="btn btn-danger">未配置</button>
                                    @else
                                        <button class="btn btn-success">已配置</button>
                                    @endif
                                </td>
                                <td>{{$web->created_at}}</td>
                                <td>{{$web->priceChange}}倍</td>
                                <td><a href="/webs/{{$web->id}}/set" class="btn btn-info">配置</a></td>
                                <td>
                                    <a href="/webs/{{$web->id}}/delete">
                                        <span class="fa fa-trash-o fa-lg text-danger"></span>
                                    </a>
                                    &nbsp;
                                    <a href="#" data-toggle="modal" data-target="#myModal" onclick="getWeb({{$web->id}})" >
                                        <span class="fa fa-pencil-square-o fa-lg text-info"></span>
                                    </a>
                                </td>
                                <td>
                                    <a href="/webs/{{$web->id}}/export?active=2" class="btn btn-default loading" >
                                        @if($web->zip == null)
                                            生成数据
                                        @else
                                            更新生成数据
                                        @endif
                                    </a>
                                </td>
                                <td>
                                    <a href="/webs/{{$web->id}}/export?active=1" class="btn btn-primary left">导出表格</a>
                                </td>
                                <td>
                                    @if($web->zip == null)
                                        <a href="javascript:(0)" class="btn btn-default">导出图片</a>
                                    @else
                                        <a href="/{{$web->zip}}" class="btn text-primary">{{$web->zip}}</a>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-success" onclick="priceChange('{{$web->url}}')" data-toggle="modal" data-target="#priceChange" >点击修改</button>
                                </td>
                                <td>
                                    <a href="#" data-toggle="modal" data-target="#getLog" onclick="getLog({{$web->id}})" >
                                        <span class="fa fa-book fa-lg text-info"></span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
{{--                    @if($noLink == 1)--}}
                        {{--{{ $products->links() }}--}}
                    {{--@endif--}}
                </div>

            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">修改网站信息</h4>
                </div>
                <div class="modal-body">
                    <form method="post" action="" id="webForm" class="form-horizontal">
                        {{csrf_field()}}
                        <input type="hidden" name="web_id" id="web_id" value="">
                        <div class="form-group">
                            <label for="url" class="col-sm-2 control-label">网站地址:</label>
                            <div class="col-sm-8">
                                <input type="text" name="url" class="form-control" id="url" value="">
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="type" class="col-sm-2 control-label">网站类型:</label>
                            <div class="col-sm-8">
                                <select id="type" name="type" class="form-control">
                                        <option value="zc">zc</option>
                                        <option value="op">op</option>
                                        <option value="ot">ot</option>
                                </select>
                            </div>
                        </div><br>
                        <div class="form-group">
                            <label for="brand_id" class="col-sm-2 control-label">品牌:</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="brand_id" id="brand_id">
                                    @foreach($brands as $brand)
                                        <option value="{{$brand->id}}">{{$brand->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div><br>
                        <div class="form-group">
                            <label for="langugae_id" class="col-sm-2 control-label">语言:</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="language_id" id="language_id">
                                    @foreach($languages as $language)
                                        <option value="{{$language->id}}">{{$language->code}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div><br>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                            <button type="submit" class="btn btn-primary">提交更改</button>
                        </div>
                    </form>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
    <div class="modal fade" id="priceChange" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">批量修改价格</h4>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="priceForm" class="form-horizontal">
                        {{csrf_field()}}
                        <input type="hidden" name="modelArrs" value="" class="modelArrs">
                        <input type="hidden" name="language_id" value="" class="batch_language_id">
                        <input type="hidden" name="active" value="1">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">输入倍数</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="mult" placeholder="1为不修改">
                            </div>
                        </div>
                        <div class="form-group" style="text-align: center">
                            <button type="submit" class="btn btn-success">提交</button>
                        </div>
                    </form>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
    <div class="modal fade" id="getLog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">{{$web->url}}操作记录</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-striped" id="tableLog">
                        <thead>
                        <tr>
                            <th>操作</th>
                            <th>时间</th>
                        </tr>
                        </thead>
                        <tbody >

                        </tbody>
                    </table>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>

@endsection



@section('foot-js')

    @if(session('success'))
        <script>
            swal('','网站创建成功!','success')
        </script>
    @endif
    @if(session('delete'))
        <script>
            swal('','删除成功!','success')
        </script>
    @endif
    @if(session('stroe'))

        <script>
            swal('','修改成功!','success')
        </script>
    @endif
    @if(session('priceChange'))
        <script>
            swal('','修改成功!','success')
        </script>
    @endif
    @if(session('false'))
        <script>
            swal('','错误!','warning')
        </script>
    @endif
    <script>

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        $('.loading').click(function () {
            $('#warning').css('display','block');
        });

        function priceChange(myurl){
            $('#priceForm').attr('action','/webs/priceChange/'+myurl);
        }

        function checkall(v) {
            $("input[name='checkProduct']").each(function(){
                this.checked=v;
            })
        }

        function getWeb(webId) {

            $.ajax({
                type: "POST",
                url: "/webs/ajax/getWeb",
                data: {web_id: webId},
                dataType: "json",
                success: function (data) {
                    $("#webForm").attr("action","/webs/"+webId+"/store");
                    $("#url").val(data.url);
                    $("#web_id").val(data.id);
                    $("#type option").each(function () {
                        var type = $(this).val();
                        if (type == data.type){
                            $(this).attr('selected',true);
                        }
                    });
                    $("#brand_id option").each(function () {
                        var brand_id = $(this).val();
                        if (brand_id == data.brand_id){
                            $(this).attr('selected',true);
                        }
                    });
                    $("#language_id option").each(function () {
                        var language_id = $(this).val();
                        if (language_id == data.language_id){
                            $(this).attr('selected',true);
                        }
                    });

                }
            });
        }

        function getLog(web_id) {
            $.ajax({
                type: "POST",
                url: "/webs/ajax/getLog",
                data: {web_id: web_id},
                dataType: "json",
                success:function (data) {
                    console.log(data);
                    var string = '';
                    $.each(data, function (index, content) {
                        string += '<tr><td>'+content.active+'</td><td>'+ content.created_at +'</td></tr>';
                    });
                    $('#tableLog tbody').html(string);
                }
            });
        }
    </script>
@endsection
