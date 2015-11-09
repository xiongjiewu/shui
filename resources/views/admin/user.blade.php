@extends('layouts.background')
@section('content')
    @if($show == 'index')
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>手机</th>
                <th>昵称</th>
                <th>头像</th>
                <th>清水值</th>
                <th>护水值</th>
                <th>黑水值</th>
                <th>备注</th>
                <th>状态</th>
                <th>详情</th>
                <th>操作</th>
                @if($type == 2)
                    <th>
                        二维码
                    </th>
                @endif
            </tr>
            </thead>
            @foreach($users as $user)
                <tr>
                    <td>{{$user['user_id']}}</td>
                    <td>{{$user['user_cellphone']}}</td>
                    <td>{{$user['user_name']}}</td>
                    <td>{{$user['image_url']? '<img src="'.$user['image_url'].'"></img>':''}}</td>
                    <td>{{$user['water_count']}}</td>
                    <td>{{$user['send_water']}}</td>
                    <td>{{$user['black_water']}}</td>
                    <td>{{$user['user_desc']}}</td>
                    <td>{{$user['status_text']}}</td>
                    <td><a href="{{route('admin::users.show',['id' => $user['user_id'],'type' => $type])}}">详情</a></td>
                    <td>
                        @if($user['is_active'])
                            <a type="status_action" href="javascript:void(0);" _u="{{$user['user_id']}}" val="0">禁用</a>
                        @else
                            <a type="status_action" href="javascript:void(0);" _u="{{$user['user_id']}}" val="1">激活</a>
                        @endif
                    </td>
                    @if($type == 2)
                        <td>
                            <a href="/download/qrcode/{{$user['user_id']}}" target="_blank">下载</a>
                        </td>
                    @endif
                </tr>
            @endforeach
        </table>
    @elseif($show == 'edit')
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>手机</th>
                <th>昵称</th>
                <th>头像</th>
                <th>清水值</th>
                <th>护水值</th>
                <th>黑水值</th>
                <th>备注</th>
                <th>状态</th>
            </tr>
            <tr class="user_edit">
                <td>{{$user['user_id']}}</td>
                <td class="user_cellphone">{{$user['user_cellphone']}}</td>
                <form id="edit_user_info" method="post" action="{{route('admin::business.update',['id' => $user['user_id'],'type' => $type])}}" enctype="multipart/form-data">
                    <td class="user_name">
                        <span>
                            {{$user['user_name']}}
                        </span>
                        <input type="text" name="user_name" value="{{$user['user_name']}}" style="display: none">
                    </td>
                    <td class="image_url">
                        @if($user['image_url'])
                            <img src="{{$user['image_url']}}">
                        @else
                        @endif
                        <input type="file" name="image" style="display: none">
                    </td>
                </form>
                <td class="water_count">{{$user['water_count']}}</td>
                <td class="send_water">{{$user['send_water']}}</td>
                <td class="black_water">{{$user['black_water']}}</td>
                <td class="user_desc">{{$user['user_desc']}}</td>
                <td>{{$user['status_text']}}</td>
            </tr>
            <tr>
                <td colspan="9" style="text-align: center">
                    <a href="javascript:void(0);" type="edit_user" _u="{{$user['user_id']}}">编辑</a>
                    <a href="javascript:void(0);" type="save_user" _u="{{$user['user_id']}}" _t="{{$type}}">保存</a>
                    @if($user['is_active'])
                        <a type="status_action" href="javascript:void(0);" _u="{{$user['user_id']}}" val="0">禁用</a>
                    @else
                        <a type="status_action" href="javascript:void(0);" _u="{{$user['user_id']}}" val="1">激活</a>
                    @endif
                </td>
            </tr>
            </thead>
        </table>
    @endif
    <script>
        new admin_user('{{route('admin::user.status.change')}}');
    </script>
@endsection