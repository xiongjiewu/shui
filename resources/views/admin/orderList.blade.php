@extends('layouts.background')
@section('content')
    <table class="table">
        <thead>
        <tr>
            <th width="50">流水ID</th>
            <th width="100">用户名</th>
            <th width="100">充值金额</th>
            <th width="100">获得清水</th>
            <th width="100">比例</th>
            <th width="100">充值状态</th>
        </tr>
        </thead>
        @foreach($list['list'] as $v)
            <tr>
                <td>
                    {{$v['order_id']}}
                </td>
                <td>
                    {{$v['name']}}
                </td>
                <td>
                    {{$v['price']}}
                </td>
                <td>
                    {{$v['water_count']}}
                </td>
                <td>
                    {{$v['rate']}}
                </td>
                <td>
                    @if($v['status'] == 1)
                        充值成功
                    @else
                        充值失败
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    {!!$list['obj']->render()!!}

@endsection