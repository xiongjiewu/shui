@extends('layouts.master')

@section('content')
    <form id="admin_login" onsubmit="return false;">
        <div class="form-group">
            <h3 class="center-block">管理员登录</h3>
            <label for="exampleInputEmail1">User Account</label>
            <input type="text" class="form-control" id="user_name" placeholder="User name or Phone">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input type="password" class="form-control" id="password" placeholder="Password">
        </div>
        <button type="submit" class="btn btn-default center-block btn-danger">Submit</button>
    </form>
    <script>
        new admin_login('{{route('admin::login::action')}}', '{{route('admin::home')}}');
    </script>
@endsection