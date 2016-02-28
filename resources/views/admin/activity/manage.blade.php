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
        @foreach($activities['list'] as $activity)
            <tr>
                <td>
                    {{$activity['activity_id']}}
                </td>
                <td>
                    {{$activity['title']}}
                </td>
                <td>
                    <textarea>{{$activity['desc']}}</textarea>
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
                    <a class="status" href="javascript:void(0);" status="{{$activity['status']}}"
                       id="{{$activity['activity_id']}}">
                        {{$activity['action_text']}}
                    </a>
                    <a href="{{route('admin::activity.edit',[$activity['activity_id']])}}">
                        编辑
                    </a>
                </td>
            </tr>
        @endforeach
    </table>
    {!!$activities['obj']->render()!!}
    <script>
        new admin_activity_manage('{{route('admin::activity.status.change')}}');
    </script>
@endsection