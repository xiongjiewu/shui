@extends('layouts.master')

@section('content')
    <div id="content">
        <div class="phone-and-code">
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1"></span>
                <input type="text" class="form-control" placeholder="手机号码" aria-describedby="basic-addon1">
            </div>
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon2"></span>
                <input type="text" class="form-control" placeholder="邀请码" aria-describedby="basic-addon2" value="{{$code}}">
            </div>
            <div class="check-code-get">
                <div class="input-group get-code">
                    <input type="text" class="form-control check-code" placeholder="验证码" aria-describedby="basic-addon3">
                </div>
                <input type="button" class="show-button">
            </div>
            <div style="width:100%;vertical-align:middle;position: relative;display: table;border-collapse: separate;height: 75px;margin-top: 150px;">
                <input type="button" class="sure-button">
            </div>
        </div>
    </div>
@endsection