@extends('layouts.background')
@section('content')
    <form action="" id="activity_add" method="post" onsubmit="return false;">
        <div class="form-group">
            <label for="title">标题</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="title">
        </div>
        <div class="form-group">
            <label for="description">描述</label>
            <input type="text" class="form-control" id="description" name="description" placeholder="description">
        </div>
        <div class="form-group">
            <label for="statement">声明</label>
            <input type="text" class="form-control" id="statement" name="statement" placeholder="statement">
        </div>
        <div class="form-group">
            <label for="state">视频</label>
            <input type="file" id="video" name="video">
        </div>
        <div class="form-group">
            <label for="image1">图片1</label>
            <input type="file" id="image1" name="image1">
        </div>
        <div class="form-group">
            <label for="image2">图片2</label>
            <input type="file" id="image2" name="image2">
        </div>
        <div class="form-group">
            <label for="image3">图片3</label>
            <input type="file" id="image3" name="image3">
        </div>
        <button type="submit" class="btn btn-default">提交</button>
    </form>
    <script>
        new admin_activity_add();
    </script>
@endsection