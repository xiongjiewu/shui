@extends('layouts.background')
@section('content')
    <form action="" id="activity_add" method="post" enctype="multipart/form-data">
        <input type="hidden" name="activity_id" value="{{isset($activity_id) ? $activity_id: ''}}">

        <div class="form-group">
            <label for="title">标题</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="title"
                   value="{{isset($base_activity['title']) ? $base_activity['title']: ''}}">
        </div>
        <div class="form-group">
            <label for="url">活动链接</label>
            <input type="text" class="form-control" id="url" name="url" placeholder="url"
                   value="{{isset($base_activity['url']) ? $base_activity['url']: ''}}">
        </div>
        <div class="form-group">
            <label for="description">描述</label>
            <input type="text" class="form-control" id="description" name="description" placeholder="description"
                   value="{{isset($base_activity['desc']) ? $base_activity['desc']: ''}}">
        </div>
        <div class="form-group">
            <label for="statement">声明</label>
            <input type="text" class="form-control" id="statement" name="statement" placeholder="statement"
                   value="{{isset($base_activity['statement']) ? $base_activity['statement']: ''}}">
        </div>
        <div class="form-group">
            <label for="price">捐赠额度</label>
            <input type="number" class="form-control" id="price" name="price" placeholder="price/单位：元"
                   value="{{isset($base_activity_fundraising['total_amount_price']) ? $base_activity_fundraising['total_amount_price']: ''}}">
        </div>
        <div class="form-group">
            <label for="video">视频</label>
            <input type="hidden" name="video_id" value="{{isset($video_id) && !empty($video_id) ? $video_id: ''}}">
            <input type="text" class="form-control" id="video" name="video" placeholder="url"
                   value="{{isset($video_url) ? $video_url: ''}}">
        </div>
        <div class="form-group">
            <label for="image1">图片1</label>
            <input type="hidden" name="image1_id" value="{{isset($image1_id) && !empty($image1_id) ? $image1_id: ''}}">
            <input type="file" id="image1" name="image1">
        </div>
        <div class="form-group">
            <label for="image2">图片2</label>
            <input type="hidden" name="image2_id" value="{{isset($image2_id) && !empty($image2_id) ? $image2_id: ''}}">
            <input type="file" id="image2" name="image2">
        </div>
        <div class="form-group">
            <label for="image3">图片3</label>
            <input type="hidden" name="image3_id" value="{{isset($image3_id) && !empty($image3_id) ? $image3_id: ''}}">
            <input type="file" id="image3" name="image3">
        </div>
        <button type="submit" class="btn btn-default">提交</button>
    </form>
    <script>
        @if(!isset($activity_id))
            new admin_activity_add();
        @endif
    </script>
@endsection