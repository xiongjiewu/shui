@extends('layouts.background')
@section('content')
    <table class="table">
        <thead>
        <tr>
            <th width="50">ID</th>
            <th width="100">亲水描述</th>
            <th width="100">亲水视频</th>
            <th width="100">支持数</th>
            <th width="100">创建时间</th>
        </tr>
        </thead>
        @foreach($activities['list'] as $activity)
            <tr>
                <td>
                    {{$activity['active_id']}}
                </td>
                <td>
                    <textarea disabled="disabled">{{$activity['content']}}</textarea>
                </td>
                <td>
                    <embed src="{{$activity['image_url']}}" type="application/x-shockwave-flash"
                           allowscriptaccess="always" allowfullscreen="true" wmode="opaque" width="250" height="100"
                           loop="true" autostart="true"></embed>
                </td>
                <td>
                    {{$activity['support']}}
                </td>
                <td>
                    {{$activity['create_time']}}
                </td>
            </tr>
        @endforeach
    </table>
    {!!$activities['obj']->render()!!}
@endsection