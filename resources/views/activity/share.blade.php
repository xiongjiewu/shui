@extends('layouts.master')

@section('content')
    <form>
        <div class="content content1">
            <div class="show-title"></div>
            <div class="phone-and-code">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"></span>
                    <input type="number" name="phone" class="form-control" placeholder="手机号码" aria-describedby="basic-addon1">
                </div>
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon2"></span>
                    <input type="text" class="form-control" disabled="disabled" placeholder="邀请码" aria-describedby="basic-addon2" value="{{$code}}">
                </div>
                <div class="check-code-get">
                    <div class="input-group get-code">
                        <input type="text" name="code" class="form-control check-code" placeholder="验证码" aria-describedby="basic-addon3">
                    </div>
                    <input type="button" class="show-button">
                </div>
                <div style="width:100%;vertical-align:middle;position: relative;display: table;border-collapse: separate;height: 75px;margin-top: 150px;">
                    <input type="button" class="sure-button">
                </div>
            </div>
        </div>
        <div class="content content2">
            <div class="title-show">
                已抢光
            </div>
            <div class="qingshuizhi-show">
                100
            </div>
            <div class="show-account">
                红包已放至账户<span class="account">13917510351</span>
                <br>
                登录APP即可使用
            </div>
            <button type="button" class="down-button"></button>
        </div>
        <div class="content content3">

        </div>
    </form>
    <script>
        new share_get('{{$share_code}}');
    </script>
@endsection