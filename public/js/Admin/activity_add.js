var admin_activity_add = function () {
    this.init();
};
admin_activity_add.prototype.init = function () {
    $('#activity_add').submit(function () {
        var title = $.trim($('#title').val());
        if (!title) {
            alert('请填写标题！');
            return false;
        }
        var url = $.trim($('#url').val());
        if (!url) {
            alert('请填写活动链接！');
            return false;
        }
        var description = $.trim($('#description').val());
        if (!description) {
            alert('请填写描述！');
            return false;
        }
        var statement = $.trim($('#statement').val());
        if (!statement) {
            alert('请填写声明！');
            return false;
        }
        var price = $('#price').val();
        if (!price) {
            alert('请填写捐赠额度！');
            return false;
        }
        var video = $('#video').val();
        if (!video) {
            alert('请选择视频！');
            return false;
        }
        var image1 = $('#image1').val();
        if (!image1) {
            alert('请选择第一张图片！');
            return false;
        }
        var image2 = $('#image2').val();
        if (!image1) {
            alert('请选择第二张图片！');
            return false;
        }
        var image3 = $('#image3').val();
        if (!image1) {
            alert('请选择第三张图片！');
            return false;
        }
        return true;
    });
};