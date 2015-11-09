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
                <th>用户邀请码</th>
                <th>公益值</th>
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
            @foreach($users['list'] as $user)
                <tr>
                    <td>{{$user['user_id']}}</td>
                    <td>{{$user['user_cellphone']}}</td>
                    <td>{{$user['user_name']}}</td>
                    <td>
                        @if($user['image_url'])
                            <img src="{{$user['image_url']}}">
                        @endif
                    </td>
                    <td>{{$user['water_count']}}</td>
                    <td>{{$user['invite_code']}}</td>
                    <td>{{$user['public_count']}}</td>
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
            <td>{!!$users['obj']->render()!!}</td>
        </table>
    @elseif($show == 'edit')
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>手机</th>
                <th>昵称</th>
                @if($type == 1)
                    <th>头像</th>
                @else
                    <th>log</th>
                    <th>营业执照</th>
                    <th>店铺实景1</th>
                    <th>店铺实景2</th>
                @endif
                <th>清水值</th>
                <th>护水值</th>
                <th>备注</th>
                <th>状态</th>
            </tr>
            <tr class="user_edit">
                <td>{{$user['user_id']}}</td>
                <td class="user_cellphone">{{$user['user_cellphone']}}</td>
                <form id="edit_user_info" method="post"
                      action="{{route('admin::business.update',['id' => $user['user_id'],'type' => $type])}}"
                      enctype="multipart/form-data">
                    <td class="user_name">
                        <span>
                            {{$user['user_name']}}
                        </span>
                        <input type="text" name="user_name" value="{{$user['user_name']}}" size="10"
                               style="display: none">
                    </td>
                    <td class="image_url">
                        @if($user['image_url'])
                            <img src="{{$user['image_url']}}">
                        @else
                        @endif
                        <input type="file" name="image" style="display: none">
                    </td>
                    @if($type == 2)
                        <td class="image_url_real1">
                            @if($user['image_url_real1'])
                                <img src="{{$user['image_url_real1']}}">
                            @else
                            @endif
                            <input type="file" name="image_url_real1" style="display: none">
                        </td>
                        <td class="image_url_real2">
                            @if($user['image_url_real2'])
                                <img src="{{$user['image_url_real2']}}">
                            @else
                            @endif
                            <input type="file" name="image_url_real2" style="display: none">
                        </td>
                        <td class="image_url_real3">
                            @if($user['image_url_real3'])
                                <img src="{{$user['image_url_real3']}}">
                            @else
                            @endif
                            <input type="file" name="image_url_real3" style="display: none">
                        </td>
                    @endif
                    <td class="water_count">
                    <span>
                        {{$user['water_count']}}
                    </span>
                        <input type="text" size="5" name="water_count" value="{{$user['water_count']}}"
                               style="display: none;">
                    </td>
                    <td class="send_water">
                    <span>
                        {{$user['send_water']}}
                    </span>
                        <input type="text" size="5" name="send_water" value="{{$user['send_water']}}"
                               style="display: none;">
                    </td>
                    <td class="user_desc">
                    <span>
                        {{$user['user_desc']}}
                    </span>
                        <textarea name="user_desc" style="display: none;width: 50px;">{{$user['user_desc']}}</textarea>
                    </td>
                </form>
                <td>{{$user['status_text']}}</td>
            </tr>
            <tr>
                <td colspan="9" style="text-align: center">
                    <a href="javascript:void(0);" type="edit_user" _u="{{$user['user_id']}}" _t="{{$type}}">编辑</a>
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