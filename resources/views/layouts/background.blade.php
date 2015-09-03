@extends('layouts.master')
@section('navigator')
    <ul class="nav nav-tabs">
        <li role="presentation" @if(!$choose_id)class="active"@endif><a href="{{route('admin::home')}}">首页</a></li>
        <li role="presentation" @if($choose_id == 1)class="active"@endif><a href="{{route('admin::users')}}">用户管理</a></li>
        <li role="presentation" @if($choose_id == 2)class="active"@endif><a href="{{route('admin::home')}}">上传文件</a></li>
    </ul>
    <div style="text-align: right">
        <a href="{{route('admin::logout')}}">退出</a>
    </div>
@endsection