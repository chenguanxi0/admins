<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="/">产品管理后台</a>
    </div>
    <!-- /.navbar-header -->

    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu">
                <li class="sidebar-search">
                    <div class="input-group custom-search-form">
                        <form action="/search" method="get">

                            <input type="text" style="width: 81%" class="form-control" name="search" placeholder="Search...">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </form>

                    </div>
                    <!-- /input-group -->
                </li>
                <li>
                    <a data-toggle="collapse" data-parent="#side-menu"
                       href="#collapseTwo"><i class="fa fa-wrench fa-fw"></i> 产品管理<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level panel-collapse collapse" id="collapseTwo">
                        <li>
                            <a data-toggle="collapse" href="#collapseMy2">所有产品<span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level panel-collapse collapse" id="collapseMy2">
                                <li>
                                    <a href="/products/brand">可用产品</a>
                                </li>
                                <li>
                                    <a href="/products/list?is_usable=0">已下架产品</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="/products/store">创建产品</a>
                        </li>
                        <li>
                            <a href="/products/import">上传产品</a>
                        </li>
                        {{--<li>--}}
                            {{--<a href="/tool/readme">上传说明</a>--}}
                        {{--</li>--}}

                    </ul>
                    <!-- /.nav-second-level -->
                </li>
                <li>
                    <a data-toggle="collapse" data-parent="#side-menu"
                       href="#collapseThree"><i class="fa fa-edit fa-fw"></i> 评论管理<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level panel-collapse collapse" id="collapseThree">
                        <li>
                            <a data-toggle="collapse" href="#collapseMy">所有评价<span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level panel-collapse collapse" id="collapseMy">
                                <li>
                                    <a href="/commits">产品评论</a>
                                </li>
                                <li>
                                    <a href="/commits/common">通用评论</a>
                                </li>

                            </ul>
                        </li>
                        <li>
                            <a href="/commits/import">导入评论</a>
                        </li>
                        <li>
                            <a href="/commits/addCommon">添加评论</a>
                        </li>


                    </ul>

                </li>
                <li>
                    <a href="/tool/uploadList"><i class="fa fa-random fa-fw "></i> 上传记录</a>
                </li>
                <li>
                    <a data-toggle="collapse" data-parent="#side-menu"
                       href="#collapseFive"><i class="fa fa-file fa-fw "></i> 网站管理<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level panel-collapse collapse" id="collapseFive">
                        <li>
                            <a href="/webs/list">所有网站</a>
                        </li>
                        <li>
                            <a href="/webs/add">新建网站</a>
                        </li>
                        <li>
                            <a href="/testWeb/add">添加检测网站</a>
                        </li>
                        <li>
                            <a href="/testWeb/index">检测网站列表</a>
                        </li>
                    </ul>

                </li>
                <li>
                    <a data-toggle="collapse" data-parent="#side-menu"
                       href="#collapseCss3"><i class="fa fa-css3 fa-fw "></i> 三要素<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level panel-collapse collapse" id="collapseCss3">
                        <li>
                            <a href="/seo/update">上传三要素</a>
                        </li>

                    </ul>

                </li>
                <li>
                    <a data-toggle="collapse" data-parent="#side-menu"
                       href="#collapseFour"><i class="fa fa-cog  fa-fw"></i> 工具<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level panel-collapse collapse" id="collapseFour">
                        <li> <a href="/tool/optionsAdd">添加属性</a></li>
                        <li> <a href="/tool/sqlApi">执行zc网站sql</a></li>
                        <li> <a href="/tool/webShell">生成网站shell</a></li>
                        <li> <a href="/tool/imdpay">修改文件</a></li>
                        <li>
                            <a href="/tool/commit">评论</a>
                        </li>
                        <li>
                            <a href="/tool/brand/add">添加品牌</a>
                        </li>
                        <li>
                            <a href="/tool/language/add">添加语言</a>
                        </li>
                        <li>
                            <a href="/tool/download/product">下载产品格式表格</a>
                        </li>
                        <li>
                            <a href="/tool/download/commit">下载产品评论格式表格</a>
                        </li>
                        <li>
                            <a href="/tool/download/common">下载通用评论格式表格</a>
                        </li>
                        <li>
                            <a href="/tool/backupbtn">备份/恢复</a>
                        </li>


                    </ul>

                </li>

            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>
    <!-- /.navbar-static-side -->
</nav>
