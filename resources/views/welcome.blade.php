<!DOCTYPE html>
<html>
<head>
    <title>Laravel</title>

    <link href="//fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            display: table;
            font-weight: 100;
            font-family: 'Lato';
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        .title {
            font-size: 96px;
        }
    </style>
</head>
<body>
<form action="http://www.lairenda.com/register" method="post" enctype="multipart/form-data">
    cellphone<input name="cellphone" type="text"><br/>
    name<input name="head_name" type="text"><br/>
    password<input name="password" type="text"><br/>
    password2<input name="password2" type="text"><br/>
    verify<input name="verify" type="text"><br/>
    head<input name="head" type="file"><br/>
    <input type="submit">
</form>
<div class="container">
    <div class="content">
        <div class="title">Laravel 5</div>
    </div>
</div>
</body>
</html>
