@extends('common.master')
<style>
    .myCheck{    zoom: 150%;}
</style>
@section('title','list')

@section('content')
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
                            @if(isset($cateArrs))
                                    <option value="{{$cateArrs[0][0]}}">{{$cateArrs[0][1]}}</option>
                            @else
                                <option value=>一级分类</option>
                            @endif
                            @foreach($categorys as $category)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-1" style="width: 9%">
                        <select class="form-control" name="category_2" id="category_2" onchange="getCategory(3)">
                            @if(isset($cateArrs[1]))
                                <option value="{{$cateArrs[1][0]}}">{{$cateArrs[1][1]}}</option>
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

            </div>

        </div>

    </div>

@endsection

@section('foot-js')

    <script>

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

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


    </script>
@endsection
