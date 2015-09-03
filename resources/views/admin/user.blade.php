@extends('layouts.background')
@section('content')
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>姓名</th>
            <th>手机</th>
            <th>类型</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        @foreach($users as $user)
            <tr>
                <td>{{$user['user_id']}}</td>
                <td>{{$user['user_name']}}</td>
                <td>{{$user['user_cellphone']}}</td>
                <td>{{$user['type_text']}}</td>
                <th>{{$user['status_text']}}</th>
                <td>
                    @if($user['is_active'])
                        <a type="status_action" href="javascript:void(0);" _u="{{$user['user_id']}}" val="0">注销</a>
                    @else
                        <a type="status_action" href="javascript:void(0);" _u="{{$user['user_id']}}" val="1">激活</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    <script>
        new admin_user('{{route('admin::user.status.change')}}');
    </script>
@endsection