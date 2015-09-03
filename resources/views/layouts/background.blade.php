<ul class="nav nav-tabs">
    <li role="presentation" @if(!$choose_id)class="active"@endif><a href="#">首页</a></li>
    <li role="presentation" @if($choose_id == 1)class="active"@endif><a href="#">用户管理</a></li>
    <li role="presentation" @if($choose_id == 2)class="active"@endif><a href="#">上传文件</a></li>
</ul>
<div style="text-align: right">
    <a href="{{route('admin::logout')}}">退出</a>
</div>