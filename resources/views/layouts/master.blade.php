<html>
<head>
    @if($show_title)
        <title>水世界管理后台 - {{$title}}</title>
    @endif
    @if($is_mobile)
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
    @endif
    <link href="/bootstrap/css/bootstrap.css" rel="stylesheet"/>
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="/bootstrap/css/bootstrap-theme.css" rel="stylesheet"/>
    <link href="/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet"/>
    @if (!empty($file_css))
        <link href="{{$file_css}}.css" rel="stylesheet"/>
    @endif
    <script src="/js/jquery-2.1.4.min.js"></script>
    <script src="/bootstrap/js/bootstrap.js"></script>
    <script src="/bootstrap/js/bootstrap.min.js"></script>
    @if (!empty($file_js))
        <script src="{{$file_js}}.js"></script>
    @endif
</head>
<body>
<div class="container">
    @yield('navigator')
    @yield('content')
</div>
</body>
</html>