@extends('layouts.background')
@section('content')
    <table class="table">
        <thead>
        <tr>
            <th width="50">ID</th>
            <th width="100">活动标题</th>
            <th>活动描述</th>
            <th width="100">活动声明</th>
            <th width="100">活动URL</th>
            <th width="100">支持数</th>
            <th width="70">状态</th>
            <th width="70">操作</th>
        </tr>
        </thead>
        @foreach($activities as $activity)
            <tr>
                <td>
                    {{$activity['activity_id']}}
                </td>
                <td>
                    {{$activity['title']}}
                </td>
                <td>
                    {{$activity['desc']}}
                </td>
                <td>
                    {{$activity['statement']}}
                </td>
                <td>
                    {{$activity['url']}}
                </td>
                <td>
                    {{$activity['focus_count']}}
                </td>
                <td>
                    {{$activity['status_text']}}
                </td>
                <td>
                    <a href="javascript:void(0);" status="{{$activity['status']}}" id="{{$activity['activity_id']}}">
                        {{$activity['action_text']}}
                    </a>
                </td>
            </tr>
        @endforeach
    </table>
    <script>
        new admin_activity_manage('{{route('admin::activity.status.change')}}');
    </script>
@endsection