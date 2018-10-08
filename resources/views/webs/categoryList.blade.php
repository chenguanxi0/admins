@extends('common.master')
<style>
    .myCheck{    zoom: 150%;}
</style>
@section('title','list')

@section('content')

@include('common.loading')
    <div class="col-lg-12 contentBox" id="list">
        <div class="panel panel-default">

            <div class="panel-heading">
                <h3 class="webId" id="{{$web->id}}">{{$web->url}}</h3>

                <form action="" method="post" id="categoryForm">
                    {{csrf_field()}}
                    <div class="col-sm-1" style="width: 9%">
                        <select class="form-control" name="language_id" id="language_id">
                            <option selected value="{{$web->language_id}}">{{$web->getLan->name}}</option>
                        </select>
                    </div>
                    <div class="col-sm-1" style="width: 9%">
                        <select class="form-control" name="category_1" id="category_1" onchange="getCategory(2)">
                            @if(isset($firstCategorys))
                                @foreach($firstCategorys as $firstCategory)
                                    @if($cateArrs[0][0] == $firstCategory->id)
                                        <option value="{{$firstCategory->id}}" selected>{{$firstCategory->name}}</option>
                                    @else
                                        <option value="{{$firstCategory->id}}">{{$firstCategory->name}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-sm-1" style="width: 9%">
                        <select class="form-control" name="category_2" id="category_2" onchange="getCategory(3)">

                            @if(isset($secondCategorys) && count($secondCategorys) > 0)
                                @if(isset($cateArrs[1]))
                                    @foreach($secondCategorys as $secondCategory)
                                        @if($secondCategory->id == $cateArrs[1][0])
                                            <option value="{{$secondCategory->id}}" selected>{{$secondCategory->name}}</option>
                                        @else
                                            <option value="{{$secondCategory->id}}" >{{$secondCategory->name}}</option>
                                        @endif
                                    @endforeach
                                @else
                                    <option value="">--请选择--</option>
                                    @foreach($secondCategorys as $secondCategory)
                                        <option value="{{$secondCategory->id}}" >{{$secondCategory->name}}</option>
                                    @endforeach
                                @endif

                            @else
                                <option value=>二级分类</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-sm-1" style="width: 9%">
                        <select class="form-control" name="category_3" id="category_3" onchange="getCategory(4)">
                            @if(isset($cateArrs[2]))
                                <option value="{{$cateArrs[2][0]}}">{{$cateArrs[2][1]}}</option>
                            @else
                                <option value=>三级分类</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-sm-1" style="width: 9%">
                        <select class="form-control" name="category_4" id="category_4" >
                            @if(isset($cateArrs[3]))
                                <option value="{{$cateArrs[3][0]}}">{{$cateArrs[3][1]}}</option>
                            @else
                                <option value=>四级分类</option>
                            @endif
                        </select>
                    </div>
                    <button class="btn btn-primary" style="margin-left: 50px;">查找</button>
                </form>
                <br>
                <button class="btn btn-success loading" style="margin-left: 15px;" onclick="add()" >添加至备选区</button>
                <button class="btn btn-success"  data-toggle="modal" data-target="#myModal" style="margin-left: 50px;">查看已选产品(<span id="myCount">{{count($web_products)}}</span>)</button>
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="myModalLabel">已选中产品</h4>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr >
                                            <th>model</th>
                                            <th>名称</th>
                                            <th>图片</th>
                                            <th>语言</th>
                                            <th>分类</th>
                                            <th>操作</th>
                                        </tr>
                                        </thead>
                                        <tbody id="tr">
                                        @foreach($web_products as $web_product)
                                            <tr id="tr{{$web_product->model}}" class="trCount">
                                                <td>{{$web_product->model}}</td>
                                                <td>{{str_limit($web_product->name,10,'...')}}</td>
                                                <td><img src="/storage/{{$web_product->getProduct->image}}" alt="" width="40"></td>
                                                <td>{{$web_product->getLanguage->code}}</td>
                                                <td>{{$web_product->getPath->name}}</td>
                                                <td><a href="javascript:(0)" onclick="del({{$web_product->model}},{{$web_product->getLanguage->id}})"><i class="fa fa-trash-o fa-lg"></i></a></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div><!-- /.modal-content -->
                    </div><!-- /.modal -->
                </div>
                <br class="clearBoth">
            </div>

        @include('common.errors')
        <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover" style="font-size: 14px;">
                        <thead>
                        <tr>
                            <th><input class="myCheck" type="checkbox" id="clickAll" onclick="checkall(this.checked)"></th>
                            <th>状态</th>
                            <th>产品名称</th>
                            <th>model</th>
                            <th>原价(美元)</th>
                            <th>现价(美元)</th>
                            <th>图片地址</th>
                            <th>当前分类</th>
                            <th>语言</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>


                        @foreach($products as $product)

                            <tr>
                                <th><input type="checkbox" class="myCheck" name="checkProduct"
                                           value="{{$product->product_model}}|{{$product->path}}|{{$product->product_name}}|{{$product->isProduct->image}}|{{$web->getLan->name}}"></th>
                                <td>
                                    @if($product->is_usable == 0)
                                        <span class="btn btn-danger">已下架</span>
                                    @else
                                        <span class="btn btn-primary">可用</span>
                                    @endif
                                </td>
                                <td>{{$product->product_name}}</td>
                                <td>{{$product->product_model}}</td>
                                <td>{{$product->isProduct->price}}</td>
                                <td>{{$product->isProduct->special_price}}</td>
                                <td><img src="/storage/{{$product->isProduct->image}}" alt="" width="30"></td>
                                <td class="path" id="{{$product->path}}">{{$product->categoryName->name}}</td>
                                <td>
                                    <a  href="/products/{{$product->product_model}}/{{$product->language_id}}">{{$product->languageName->code}}</a>
                                </td>
                                <td>
                                    <a href="#" data-toggle="modal" data-target="#myModal2" onclick="getProduct({{$product->product_model}},{{$product->language_id}})" >
                                        <span class="fa fa-pencil-square-o fa-lg text-info"></span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach


                        </tbody>
                    </table>
                    @if($noLink == 1)
                        {{ $products->links() }}
                    @endif
                </div>

            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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

@endsection

@section('foot-js')
    @if(session('noImage'))
        <script>
            swal('','图片不存在!','error')
        </script>
    @endif
    @if(session('update'))
        <script>
            swal('','修改成功!','success')
        </script>
    @endif

    <script>
        $('.loading').click(function () {
            $('#warning').css('display','block');
        });
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });
        function del(model,language_id){
            $('#tr'+model).remove();
            $.ajax({
                type: "post",
                url: "/webs/delSelect",
                data: {model: model,language_id:language_id},
                dataType: "json",
                success: function (data) {
                    if (!data){
                        alert("删除失败!请重试");
                    }
                    var count = $('.trCount').length;
                    $('#myCount').html(count);
                }
            });
        }
        function add() {
            $('#warning').css('display','block');
            var  box = $("input[name='checkProduct']");
            length =box.length;
            var models ="";
            for(var i=0;i<length;i++){
                if(box[i].checked==true){
                    models +=box[i].value+",";
                }
            }
            var web_id = $('.webId').attr('id');
            var language_id = $('#language_id').val();
            $.ajax({
                type: "post",
                url: "/webs/addSelect",
                data: {models: models,language_id:language_id,web_id:web_id},
                dataType: "json",
                success: function (data) {
                    location.reload()
                }
            });
        }

        function getCategory(cid) {

            var web_id = $('.webId').attr('id');
            var category_id = 'category_' + (cid - 1);
            var category_val = $('#' + category_id).val();
            $("#categoryForm").attr('action','/webs/'+web_id+'/'+category_val+'/set');
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
                    $("#name").val(data.product_name);
                    $("#price").val(data.price);
                    $("#special_price").val(data.special_price);
                    $("#product_description").val(data.product_description);
                }
            });
        }

    </script>
@endsection
