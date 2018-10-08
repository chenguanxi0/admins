@extends('common.master')

@section('title','list')

@section('content')
    <span style="display: none" id="cid" value="{{$category->id}}"></span>
    <div class="col-lg-12 contentBox" id="list">
        <div class="panel panel-default">
            <span style="display: none" id="span_status" value="{{$status}}"></span>
            <div class="panel-heading">
                <h3 id="brand" value="{{$brand_id}}">产品列表({{\App\Brand::where('id',$brand_id)->first()->name}})</h3>

                <form action="" method="post" id="categoryForm">
                    {{csrf_field()}}
                    <div class="col-sm-1" style="width: 9%">
                        <select class="form-control" name="language_id" id="language_id" onchange="getvalue(this)">
                            @foreach($languages as $language)
                                @if($lanArrs[0] == $language->id)
                                    <option value="{{$language->id}}" selected>{{$language->code}}</option>
                                @else
                                    <option value="{{$language->id}}" >{{$language->code}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-1" style="width: 13%">
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
                    <div class="col-sm-1" style="width: 13%">
                        <select class="form-control" name="category_2" id="category_2" onchange="getCategory(3)">
                            @if(count($secondCategorys) > 0)

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
                    <div class="col-sm-1" style="width: 13%">
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
                    <div class="col-sm-1" style="width: 9%">
                        <select class="form-control" name="status" id="status" onchange="statuschange()">
                            <option value="0">普通产品</option>
                            <option value="1">广告图产品</option>
                            <option value="2">特价产品</option>
                            <option value="3">最新产品</option>
                            <option value="4">最热产品</option>
                        </select>
                    </div>
                    <div class="col-sm-1" style="width: 9%">
                        <button class="btn btn-primary" style="margin-left: 50px;">所有产品</button>
                    </div>
                </form>
                <br class="clearBoth"><br class="clearBoth">
                <form action="/search" method="get" style="margin-left: 15px;">
                    <input type="hidden" name="lan" value="{{$lanArrs[0]}}">
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
                <button class="btn btn-success" onclick="batch()" data-toggle="modal" data-target="#batch_price" style="margin-left: 50px;">批量添加评论</button>
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
                            <th>成本价(RMB)</th>
                            <th>邮费(RMB)</th>
                            <th>现价(美元)</th>

                            <th>图片地址</th>
                            <th>当前分类</th>
                            <th>语言</th>
                            <th>编辑产品</th>
                            <th>添加评论</th>
                            <th>展示评论数</th>
                            <th>间隔天数</th>
                            <th>评论(有图|无图|总数)</th>
                            <th>同步价格</th>
                            <th>日志</th>
                        </tr>
                        </thead>
                        <tbody>


                        @foreach($products as $product)

                            @if($product->isProduct->sureChange == 0)
                            <tr class="bg-danger">
                                @else
                            <tr>
                            @endif
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
                                        <span class="btn btn-default">普通</span>
                                    @elseif($product->status == 1)
                                        <span class="btn btn-primary">广告图</span>
                                    @elseif($product->status == 2)
                                        <span class="btn btn-success">特价</span>
                                    @elseif($product->status == 3)
                                        <span class="btn btn-info">最新</span>
                                    @elseif($product->status == 4)
                                        <span class="btn btn-danger">最热</span>
                                    @endif
                                </td>
                                {{--                                <td>{{$product->language_description_1()[0]->product_name}}</td>--}}
                                <td>{{$product->product_name}}</td>
                                <td>{{$product->product_model}}</td>
                                <td>{{$product->isProduct->price}}</td>
                                <td>{{$product->isProduct->costPrice}}</td>
                                <td>{{$product->isProduct->freight}}</td>
                                <td>{{$product->isProduct->special_price}}</td>

                                <td><img src="/storage/{{$product->isProduct->image}}" alt="" width="50"></td>
                                <td id="{{$product->path}}">{{$product->categoryName->name}}</td>
                                <td>
                                    <a class="" href="/products/{{$product->product_model}}/{{$product->language_id}}">{{$product->languageName->code}}</a>
                                </td>
                                <td class="text-center">
                                    <a href="#" data-toggle="modal" data-target="#myModal" onclick="getProduct({{$product->product_model}},{{$product->language_id}})" >
                                        <span class="fa fa-pencil-square-o fa-lg text-info"></span>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="#" data-toggle="modal" data-target="#addCommit" onclick="addCommit({{$product->product_model}},{{$product->language_id}})">
                                        <span class="fa fa-pencil-square-o fa-lg text-info"></span>
                                    </a>
                                </td>
                                <td class="text-center">{{$product->isProduct->commitsNum}}条</td>
                                <td class="text-center">{{$product->isProduct->days}}天</td>
                                <td class="text-center">{{$product->havImg}}|{{$product->noImg}}|{{$product->havImg+$product->noImg}}</td>
                                <td class="text-center">
                                    @if($product->isProduct->sureChange == 0)
                                        <form action="/products/productPriceSure" method="post">
                                            {{csrf_field()}}
                                            <input type="hidden" name="model" value="{{$product->product_model}}">
                                            <input type="hidden" name="special_price" value="{{$product->isProduct->special_price}}">
                                            <button type="submit" class="loading"><span class="fa fa-refresh fa-lg"></span></button>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    @if($product->isProduct->getLog)
                                    <a href="#" data-toggle="modal" data-target="#getLog" onclick="getLog({{$product->product_model}})" >
                                        <span class="fa fa-book fa-lg text-info"></span>
                                    </a>
                                    @endif
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
                                <input type="text" name="price" class="form-control" id="price" value="" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="costPrice" class="col-sm-2 control-label">成本价:</label>
                            <div class="col-sm-8">
                                <input type="text"  name="costPrice" class="form-control" id="costPrice" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="freight" class="col-sm-2 control-label">邮费:</label>
                            <div class="col-sm-8">
                                <input type="text" name="freight" class="form-control" id="freight" value="">
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
                        <input type="hidden" name="language_id" value="" class="batch_language_id">
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
                        <div class="form-group">
                            <label class="col-sm-2 control-label">条数:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="commitsNum" placeholder="展示评论条数">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">天数:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="days" placeholder="评论间隔天数">
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
                    <h4 class="modal-title" id="myModalLabel">批量添加评论</h4>
                </div>
                <div class="modal-body">
                    <form action="/commits/addManyCommonCommits" method="post" class="form-horizontal">
                        {{csrf_field()}}
                        <input type="hidden" name="modelArrs" value="" class="modelArrs">
                        <input type="hidden" name="language_id" value="" class="batch_language_id">
                        <input type="hidden" name="brand_id" value="" class="brand_id">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">请输入:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="num" placeholder="每个产品添加个数">
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
                    <h4 class="modal-title" id="myModalLabel">操作记录</h4>
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
    <div class="modal fade" id="addCommit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">请选择</h4>
                </div>
                <div class="modal-body text-center">
                    <a class="btn btn-success" href="#" data-dismiss="modal" aria-hidden="true" data-toggle="modal" data-target="#myModal2"  >
                        添加产品评论
                    </a>
                    <a class="btn btn-success" href="#" data-dismiss="modal" aria-hidden="true" data-toggle="modal" data-target="#addCommonCommit"  >
                    添加通用评论</a>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
    <div class="modal fade" id="addCommonCommit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">随机通用评论</h4>
                </div>
                <div class="modal-body text-center">

                    <div class="form-group">
                    <label class="col-sm-2 control-label">输入随机个数</label>
                    <div class="col-sm-8">
                        <input type="text" name="num" class="form-control">
                        <input type="hidden" name="randModel" value="">
                    </div>
                    </div>
                    <br>
                    <div class="form-group text-center">
                        <a class="btn btn-primary" data-dismiss="modal" aria-hidden="true" data-toggle="modal" data-target="#CommonCommit" onclick="addCommonCommit()">生成随机评论</a>
                    </div>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
    <div class="modal fade" id="CommonCommit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">随机通用评论</h4>
                </div>
                <div class="modal-body text-center">
                    <table class="table table-striped" id="commonCommitTable">
                        <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">使用次数</th>
                        </tr>
                        </thead>
                        <tbody >

                        </tbody>
                        <form action="/commits/addCommonCommits" method="post">
                            {{csrf_field()}}
                            <button type="submit" class="btn btn-success">确认添加</button>
                        </form>
                    </table>
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
    @if(session('priceChange'))
        <script>
            swal('','同步成功!','success')
        </script>
    @endif
    @if(session('addSuccess'))
        <script>
            swal('','添加评论成功!','success')
        </script>
    @endif
    @if(session('addFail'))
        <script>
            swal('','没有该语言的评论!','error')
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
            $("input[name='randModel']").val(productModel);
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
            var brand_id = $('#brand').attr('value');
            $('.modelArrs').val(models);
            $('.batch_language_id').val(language_id);
            $('.brand_id').val(brand_id);
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
                    if(data.costPrice == null && data.freight == null){
                        $("#costPrice").attr('disabled',true);
                        $("#freight").attr('disabled',true);
                        $("#special_price").val(data.special_price);
                    }else {
                        $("#special_price").attr('disabled',true);
                        $("#costPrice").val(data.costPrice);
                        $("#freight").val(data.freight);
                    }
                    $("#product_description").val(data.product_description);
                }
            });
        }
        $('.loading').click(function () {
            $('#warning').css('display','block');
        });
        function statuschange() {
            var status = $('#status').val();
            var category_val = $('#cid').attr('value');
            $("#categoryForm").attr('action','/products/'+category_val+'/'+status+'/list');
        }
        function getLog(model) {
            $.ajax({
                type: "POST",
                url: "/products/ajax/getLog",
                data: {model: model},
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
        function productPriceSure(model,$mult){
            $.ajax({
                type: "POST",
                url: "/products/productPriceSure",
                data: {model: model,mult:$mult},
                dataType: "json",
                success:function (data) {

                }
            });
        }
        function addCommonCommit(){
            var brand_id = $('#brand').attr('value');
            var language_id = $('#language_id').val();
            var num = $("input[name='num']").val();
            var model = $("input[name='randModel']").val();
            $.ajax({
                type: "POST",
                url: "/commits/getCommonCommits",
                data:{brand_id:brand_id,language_id:language_id,num:num,model:model},
                dataType: "json",
                success:function (data) {
                    var string = '';
                    $.each(data, function (index, content) {
                        string += '<tr><td>'+content.id+'</td><td>'+ content.hasUse +'</td></tr>';
                    });
                    $('#commonCommitTable tbody').html(string);
                }
            });
        }
    </script>
@endsection
