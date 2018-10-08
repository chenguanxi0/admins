@extends('common.master')

@section('title','list')

@section('content')
    <span style="display: none" id="cid" value=""></span>
    <div class="col-lg-12 contentBox" id="list">
        <div class="panel panel-default">

            <div class="panel-heading">
                <h3 id="brand_id" value="{{request('brand_id')}}">产品列表({{\App\Brand::where('id',request('brand_id'))->first()->name}})</h3>

                <form action="" method="post" id="categoryForm">
                    {{csrf_field()}}
                    <div class="col-sm-1" style="width: 9%">
                    <select class="form-control" name="language_id" id="language_id" onchange="getvalue(this)">
                        @if(isset($lanArrs))
                            <option value="{{$lanArrs[0]}}">{{$lanArrs[1]}}</option>
                        @else
                            <option value="all">所有语言</option>
                        @endif
                        @foreach($languages as $language)
                            <option value="{{$language->id}}">{{$language->code}}</option>
                        @endforeach
                    </select>
                    </div>
                    <div class="col-sm-1" style="width: 13%">
                        <select class="form-control" name="category_1" id="category_1" onchange="getCategory(2)">
                            @if(isset($cateArrs))
                                <option value="{{$cateArrs[0][0]}}">{{$cateArrs[0][1]}}</option>
                            @else
                                <option value=>一级分类</option>
                            @endif
                            @if(isset($categorys))
                            @foreach($categorys as $category)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach
                                @endif
                        </select>
                    </div>
                    <div class="col-sm-1" style="width: 13%">
                        <select class="form-control" name="category_2" id="category_2" onchange="getCategory(3)">
                            @if(isset($cateArrs[1]))
                                <option value="{{$cateArrs[1][0]}}">{{$cateArrs[1][1]}}</option>
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
                    <div class="col-sm-1" style="width: 13%">
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
                            <option value="">所有属性</option>
                            <option value="0">普通产品</option>
                            <option value="1">广告图产品</option>
                            <option value="2">特价产品</option>
                            <option value="3">最新产品</option>
                            <option value="4">最热产品</option>
                        </select>
                    </div>
                    <button class="btn btn-primary" style="margin-left: 50px;">查找</button>
                </form>
                <br class="clearBoth">
            </div>

        @include('common.errors')
        <!-- /.panel-heading -->

            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>


@endsection

@section('foot-js')


    <script>

            $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
            });

            function getvalue() {
                var brand_id = $('#brand_id').attr('value');

                if ($('#language_id').val() == 'all') {
                    window.location.reload();
                }
                $.ajax({
                    type: "post",
                    url: "/products/ajax/language",
                    data: {language_id: $('#language_id').val(),brand_id:brand_id},
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
                $("#categoryForm").attr('action','/products/'+category_val+'/6/list');
                $('#cid').attr('value',category_val);
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

            function statuschange() {
                var status = $('#status').val();
                var category_val = $('#cid').attr('value');
                $("#categoryForm").attr('action','/products/'+category_val+'/'+status+'/list');
            }

    </script>
@endsection
