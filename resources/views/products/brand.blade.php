@extends('common.master')

@section('title','brand')

@section('content')
    <div class="col-lg-12 contentBox" id="list">
        <div class="panel panel-default">

            <div class="panel-heading">
                <h3>所有品牌</h3>
                <br class="clearBoth">
            </div>

        @include('common.errors')
        <!-- /.panel-heading -->
            <ul class="list-group">

                @foreach(\App\Brand::all() as $brand)
                <li class="list-group-item"><a href="/products/list?brand_id={{$brand->id}}">{{$brand->name}}</a></li>
                @endforeach
            </ul>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
@endsection

@section('foot-js')
    @if(session('success'))
        <script>
            swal('','数据上传成功!','success')
        </script>
    @endif

@endsection
