@extends('layouts.master')

@section('content')
    <form>
        <div class="content content1" style="display: none;">
            <div class="show-title"></div>
            <div class="phone-and-code">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"></span>
                    <input type="text" name="phone" maxlength="11" class="form-control" placeholder="手机号码" aria-describedby="basic-addon1">
                </div>
                <div class="input-group">
                    <span class="input-group-addon">邀请码</span>
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
        <div class="content content2" style="display: block;">
            <div class="title-show" style="display: none">
                已抢光
            </div>
            <div class="qingshuizhi-show" style="display: block">
                100
            </div>
            <div class="show-account" style="display: block">
                红包已放至账户<span class="account">13917510351</span>
                <br>
                登录APP即可使用
            </div>
            <a href="http://www.lairenda.com/download/sxsj_1.0.apk">
                <button type="button" class="down-button"></button>
            </a>
        </div>
        <div class="content content3">
            <div class="show-list">
                <table>
                    <tr class="first">
                        <td class="user-img">
                            <img src="/image/touxiang_03.png">
                        </td>
                        <td class="user-info">
                            <div class="time-name">
                                <span class="name">
                                杨帆
                                </span>
                                <span class="time">
                                    10.18 12:12
                                </span>
                            </div>
                            <div class="time-content">
                                磁暴知应天上有
                            </div>
                        </td>
                        <td class="user-water">
                            12121
                        </td>
                    </tr>
                    <tr>
                        <td class="user-img">
                            <img src="/image/touxiang_03.png">
                        </td>
                        <td class="user-info">
                            <div class="time-name">
                                <span class="name">
                                杨帆
                                </span>
                                <span class="time">
                                    10.18 12:12
                                </span>
                            </div>
                            <div class="time-content">
                                磁暴知应天上有
                            </div>
                        </td>
                        <td class="user-water">
                            12121
                        </td>
                    </tr>
                    <tr>
                        <td class="user-img">
                            <img src="/image/touxiang_03.png">
                        </td>
                        <td class="user-info">
                            <div class="time-name">
                                <span class="name">
                                杨帆
                                </span>
                                <span class="time">
                                    10.18 12:12
                                </span>
                            </div>
                            <div class="time-content">
                                磁暴知应天上有
                            </div>
                        </td>
                        <td class="user-water">
                            12121
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </form>
    <script>
        new share_get('{{$share_code}}');
    </script>
@endsection