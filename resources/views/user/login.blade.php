<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Laravel</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">


</head>

<body>
    <div class="col-lg-12 contentBox" id="detail">
        <div class="form row">
            <form class="form-horizontal col-sm-offset-3 col-md-offset-3" id="login_form" action="/login" method="post">
                {{csrf_field()}}
                <h3 class="form-title">登录</h3>
                <div class="col-sm-9 col-md-9">
                    <div class="form-group">
                        <i class="fa fa-user fa-lg"></i>
                        <input class="form-control required" type="text" placeholder="Username" name="username" autofocus="autofocus" maxlength="20"/>
                    </div>
                    <div class="form-group">
                        <i class="fa fa-lock fa-lg"></i>
                        <input class="form-control required" type="password" placeholder="Password" name="password" />
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-success pull-right" value="Login "/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>


</html>