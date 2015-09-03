@extends('layouts.background')
@section('content')
    <div class="jumbotron">
        <h1>欢迎回来, {{$user_name}}!</h1>

        <p>新的一天笑一个</p>

        <p><a class="btn btn-primary btn-lg" href="{{route('admin::users')}}" role="button">用户管理</a></p>
    </div>
@endsection