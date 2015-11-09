var admin_user = function (action_url) {
    this.action_url = action_url;
    this.init();
};
admin_user.prototype.init = function () {
    var that = this;
    $('a[type="status_action"]').each(function () {
        $(this).click(function () {
            if (confirm('Are you sure?')) {
                var val = $(this).attr('val');
                var user_id = $(this).attr('_u');
                $.ajax({
                    url: that.action_url,
                    type: 'post',
                    data: {type: val, user_id: user_id},
                    dataType: 'json',
                    success: function (res) {
                        alert(res.msg);
                        if (res.status) {
                            window.location.reload();
                        }
                    }
                });
            }
        });
    });
    $('a[type="save_user"]').click(function () {
        var user_id = $(this).attr('_u');
        if (!$('td.user_name').has('input').length) {
            alert('编辑后方可保存');
            return false;
        }
        var user_name = $($('td.user_name').find('input').get(0)).val();
        if (!user_name) {
            alert('昵称不能为空！');
            return false;
        }
        var image_url = $($('td.image_url').find('input').get(0)).val();
        if (!image_url) {
            alert('请上传图片！');
            return false;
        }
        $('#edit_user_info').submit();
    });
    $('a[type="edit_user"]').click(function () {
        $('td.user_name span').hide();
        $('td.user_name input').show();
        $('td.image_url img').hide();
        $('td.image_url input').show();
        $(this).hide();
    });
};