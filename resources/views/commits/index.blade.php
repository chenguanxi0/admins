@extends('common.master')
@section('title','commits')
@section('content')
   <div class="col-lg-12 contentBox" id="list">
      <div class="panel panel-default">

         <div class="panel-heading">
            <h3>评论列表</h3>
            <form action="/commits/search" method="get">
               <div class="input-group col-md-2" style="margin-top:0px;positon:relative">
                  <input type="text" class="form-control" placeholder="输入model" name="model" >
                  <span class="input-group-btn">
               <button type="submit" class="btn btn-info btn-search">查找</button>
            </span>
               </div>
            </form>

            <br class="clearBoth">
         </div>

      @include('common.errors')
      <!-- /.panel-heading -->
         <div class="panel-body">
            <div class="table-responsive">
               <table class="table table-hover">
                  <thead>
                  <tr>
                     <th>model</th>
                     <th>语言</th>
                     <th>用户名</th>
                     <th>评论内容</th>
                     <th>时间</th>
                     <th>回复内容</th>
                     <th>回复时间</th>
                     <th>星级</th>
                     <th>来源</th>
                     <th>操作</th>
                  </tr>
                  </thead>
                  <tbody>
                     @foreach($commits as $k=>$commit)
                        <tr>
                           <td>{{$commit->model}}</td>
                           <td>{{$commit->getLan->code}}</td>
                           <td>{{$commit->username}}</td>
                           <td>{{str_limit($commit->content,40,'...')}}</td>
                           <td>
                              {{$commit->created_at}}
                           </td>
                           <td>{{$commit->reply}}</td>
                           <td>{{$commit->replyTime}}</td>
                           <td>{{$commit->star}}</td>
                           <td>
                              @if($commit->is_admin == 0)
                                 <span class="text-danger">user</span>
                              @else
                                 <span class="text-primary">admin</span>
                              @endif
                           </td>
                           <td>
                              <a href="/commits/{{$commit->id}}/delete">
                                 <span class="fa fa-trash-o fa-lg text-danger"></span>
                              </a>
                           </td>
                        </tr>
                     @endforeach
                  </tbody>
               </table>
                  {{$commits->links()}}
            </div>

         </div>
         <!-- /.panel-body -->
      </div>
      <!-- /.panel -->
   </div>
@endsection

@section('foot-js')

   @if(session('add'))
      <script>
          swal('','评论添加成功!','success')
      </script>
   @endif
   @if(session('success'))
      <script>
          swal('','删除成功!','success')
      </script>
   @endif

@endsection