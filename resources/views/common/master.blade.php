<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/sb-admin-2.css">
    <link rel="stylesheet" href="/css/font-awesome.css">
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/style.css">

</head>

<body>

@include('common.navbar')

<div id="page-wrapper">

    <div class="row" style="margin-top: 20px;">
        @yield('content')
    </div>


</div>

@include('common.footer')


</body>


</html>