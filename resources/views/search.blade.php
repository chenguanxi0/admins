@extends('common.master')

@section('title','list')

@section('content')

    <div class="col-lg-12 contentBox" id="list">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>产品列表</h3>
                <form action="/search" method="get" style="margin-left: 15px;">
                    <input type="hidden" name="lan" value="{{request('lan')}}">
                    <div class="input-group col-md-8" style="margin-top:0px;positon:relative">
                        <input type="text" class="form-control" placeholder='输入model,用","隔开,必须是同一语言' name="model" >
                        <span class="input-group-btn">
               <button type="submit" class="btn btn-info btn-search">查找</button>
                </span>
                    </div>
                </form>
                <br class="clearBoth">
                <button class="btn btn-success loading" style="margin-left: 15px;" onclick="batch()" data-toggle="modal" data-target="#batch_statu">批量修改属性</button>
                <span>(至少选择两项)</span>
                <button class="btn btn-success" onclick="batch()" data-toggle="modal" data-target="#batch_price" style="margin-left: 50px;">批量修改价格</button>
                <span>(至少选择两项)</span>
            </div>
        @include('common.errors')
        <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th><input type="checkbox" id="clickAll" onclick="checkall(this.checked)"></th>
                            <th>状态</th>
                            <th>属性</th>
                            <th>产品名称</th>
                            <th>model</th>
                            <th>原价(美元)</th>
                            <th>现价(美元)</th>
                            <th>图片地址</th>
                            <th>当前分类</th>
                            <th>语言</th>
                            <th>编辑产品</th>
                            <th>添加评论</th>
                            <th>评论(有图|无图|总数)</th>
                        </tr>
                        </thead>
                        <tbody>


                        @foreach($products as $product)

                            <tr>
                                <th><input type="checkbox" name="checkProduct" value="{{$product->product_model}}"></th>
                                <td>
                                    @if($product->is_usable == 0)
                                        <span class="btn btn-danger">已下架</span>
                                    @else
                                        <span class="btn btn-primary">可用</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->status == 0)
                                        <span class="btn btn-info">普通</span>
                                    @elseif($product->status == 1)
                                        <span class="btn btn-info">广告图</span>
                                    @elseif($product->status == 2)
                                        <span class="btn btn-info">特价</span>
                                    @elseif($product->status == 3)
                                        <span class="btn btn-info">最新</span>
                                    @elseif($product->status == 4)
                                        <span class="btn btn-info">最热</span>
                                    @endif
                                </td>
                                {{--                                <td>{{$product->language_description_1()[0]->product_name}}</td>--}}
                                <td>{{$product->product_name}}</td>
                                <td>{{$product->product_model}}</td>
                                <td>{{$product->isProduct->price}}</td>
                                <td>{{$product->isProduct->special_price}}</td>
                                <td><img src="/storage/{{$product->isProduct->image}}" alt="" width="50"></td>
                                <td id="{{$product->path}}">{{$product->categoryName->name}}</td>
                                <td>
                                    <a class="" href="/products/{{$product->product_model}}/{{$product->language_id}}">{{$product->languageName->code}}</a>
                                </td>
                                <td style="text-align: center">
                                    <a href="#" data-toggle="modal" data-target="#myModal" onclick="getProduct({{$product->product_model}},{{$product->language_id}})" >
                                        <span class="fa fa-pencil-square-o fa-lg text-info"></span>
                                    </a>
                                </td>
                                <td style="text-align: center">
                                    <a href="#" data-toggle="modal" data-target="#myModal2" onclick="addCommit({{$product->product_model}},{{$product->language_id}})" >
                                        <span class="fa fa-pencil-square-o fa-lg text-info"></span>
                                    </a>
                                </td>
                                <td style="text-align: center">{{$product->havImg}}|{{$product->noImg}}|{{$product->havImg+$product->noImg}}</td>
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

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">修改产品信息</h4>
                </div>
                <div class="modal-body">
                    <form method="post" action="" id="productForm" class="form-horizontal">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label">产品是否可用</label>

                            <div class="col-sm-8">
                                <label class="radio-inline ">
                                    <input type="radio" name="is_usable" id="is_usable1" value="1">可用
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_usable" id="is_usable2" value="0">下架
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">产品属性</label>

                            <div class="col-sm-8">
                                <label class="radio-inline ">
                                    <input type="radio" name="radio_status" id="" value="0">普通
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="radio_status" id="" value="1">广告图
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="radio_status" id="" value="2">特价
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="radio_status" id="" value="3">最新
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="radio_status" id="" value="4">最热
                                </label>

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">产品名称:</label>
                            <div class="col-sm-8">
                                <input type="text" name="product_name" class="form-control" id="name" value="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="price" class="col-sm-2 control-label">产品原价:</label>
                            <div class="col-sm-8">
                                <input type="text" name="price" class="form-control" id="price" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="special_price" class="col-sm-2 control-label">产品现价:</label>
                            <div class="col-sm-8">
                                <input type="text" name="special_price" class="form-control" id="special_price" value="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="product_description" class="col-sm-2 control-label">产品描述:</label>
                            <div class="col-sm-8">
                                    <textarea name="product_description" rows="4" class="form-control" id="product_description" >

                                    </textarea>
                            </div>
                        </div>

                        <br>
                        <div class="form-group" style="text-align: center">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-pencil"></i> 更新产品
                            </button>
                        </div>

                    </form>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">添加评论</h4>
                </div>
                <div class="modal-body">
                    <form action="/commits/add" method="post" class="form-horizontal">
                        {{csrf_field()}}
                        <input type="hidden" name="model" value="" id="model">
                        <input type="hidden" name="language_id" value="" >
                        <div class="form-group">
                            <label for="star" class="col-sm-2 control-label">评分:</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="star">
                                    <option value="5">5星</option>
                                    <option value="4">4星</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="username" class="col-sm-2 control-label">用户名称:</label>
                            <div class="col-sm-8">
                                <input type="text" name="username" class="form-control" id="username" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="content" class="col-sm-2 control-label">评论内容:</label>
                            <div class="col-sm-8">
                                <textarea name="content" id="content" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reply" class="col-sm-2 control-label">回复内容:</label>
                            <div class="col-sm-8">
                                <textarea name="reply" id="reply" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="img" class="col-sm-2 control-label">图片地址(用|分隔):</label>
                            <div class="col-sm-8">
                                <input type="text" name="img" class="form-control" id="img" value="">
                            </div>
                        </div>
                        <div class="form-group" style="text-align: center">
                            <button type="submit" class="btn btn-success">添加</button>
                        </div>
                    </form>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
    <div class="modal fade" id="batch_statu" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">批量修改状态</h4>
                </div>
                <div class="modal-body">
                    <form action="/products/batch" method="post" class="form-horizontal">
                        {{csrf_field()}}
                        <input type="hidden" name="modelArrs" value="" class="modelArrs">
                        <input type="hidden" name="language_id" value="{{request('lan')}}" class="batch_language_id">
                        <input type="hidden" name="active" value="0">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">产品属性</label>
                            <div class="col-sm-8">
                                <label class="radio-inline ">
                                    <input type="radio" name="radio_status" id="" value="0">普通
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="radio_status" id="" value="1">广告图
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="radio_status" id="" value="2">特价
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="radio_status" id="" value="3">最新
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="radio_status" id="" value="4">最热
                                </label>
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
    <div class="modal fade" id="batch_price" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">批量修改价格</h4>
                </div>
                <div class="modal-body">
                    <form action="/products/batch" method="post" class="form-horizontal">
                        {{csrf_field()}}
                        <input type="hidden" name="modelArrs" value="" class="modelArrs">
                        <input type="hidden" name="language_id" value="{{request('lan')}}" class="batch_language_id">
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
@endsection

@section('foot-js')

    @if(session('hasProduct'))
        <script>
            swal('','产品重复!','success')
        </script>
    @endif
    @if(session('delete'))
        <script>
            swal('','成功删除产品!','success')
        </script>
    @endif
    @if(session('success'))
        <script>
            swal('','上传成功!','success')
        </script>
    @endif
    @if(session('update'))
        <script>
            swal('','修改成功!','success')
        </script>
    @endif
    @if(session('add'))
        <script>
            swal('','添加成功!','success')
        </script>
    @endif

    <script>

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });
        $(document).ready(function () {
            var statu = $('#span_status').attr('value');
            $('#status option').each(function () {
                if (statu == $(this).attr('value')){
                    $(this).attr('selected',true);
                }
            });
        })

        function getvalue() {
            if ($('#language_id').val() == 'all') {
                window.location.reload();
            }
            $.ajax({
                type: "post",
                url: "/products/ajax/language",
                data: {language_id: $('#language_id').val()},
                dataType: "json",
                success: function (data) {
                    var string = '<option value=>--请选择--</option>';
                    if(data.length){
                        $.each(data, function (index, content) {
                            string += '<option value=' + content.id + '>' + content.name + '</option>';
                            $('#category_1').html(string);
                        });
                    }else {
                        $('#category_1').html(string);
                    }

                }
            });
        }

        function getCategory(cid) {

            var category_id = 'category_' + (cid - 1);
            var category_val = $('#' + category_id).val();
            $("#categoryForm").attr('action','/products/'+category_val+'/0/list');

            $.ajax({
                type: "POST",
                url: "/products/ajax/getCategory",
                data: {category_id: category_val},
                dataType: "json",
                success: function (data) {
                    var string = '<option value=>--请选择--</option>';
                    if(data.length){
                        $.each(data, function (index, content) {
                            string += '<option value=' + content.id + '>' + content.name + '</option>';
                            $('#' + 'category_' + cid).html(string);
                        });
                    }else{
                        $('#' + 'category_' + cid).html(string);
                    }

                }
            });
        }

        function checkall(v) {
            $("input[name='checkProduct']").each(function(){
                this.checked=v;
            })
        }

        function addCommit(productModel,languageId) {
            $("#model").val(productModel);
            $("input[name='language_id']").val(languageId);
        }
        function batch(){
            var  box = $("input[name='checkProduct']");
            length =box.length;
            var models ="";
            for(var i=0;i<length;i++){
                if(box[i].checked==true){
                    models +=box[i].value+",";
                }
            }
            var language_id = $('#language_id').val();
            $('.modelArrs').val(models);

        }
        function getProduct(productModel,languageId) {

            $.ajax({
                type: "POST",
                url: "/products/ajax/getProduct",
                data: {product_model: productModel,language_id:languageId},
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $("#productForm").attr("action","/products/"+productModel+"/"+languageId+"/update");
                    $("input[name='is_usable']").each(function () {
                        if ($(this).val() == data.is_usable){
                            $(this).attr('checked',true)
                        }
                    });
                    $("input[name='radio_status']").each(function () {
                        if ($(this).val() == data.status){
                            $(this).attr('checked',true)
                        }
                    });
                    $("#name").val(data.product_name);
                    $("#price").val(data.price);
                    $("#special_price").val(data.special_price);
                    $("#product_description").val(data.product_description);
                }
            });
        }
        function statuschange() {
            var status = $('#status').val();
            var category_val = $('#cid').attr('value');
            $("#categoryForm").attr('action','/products/'+category_val+'/'+status+'/list');
        }
    </script>
@endsection
