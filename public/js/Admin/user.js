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
        var type = $(this).attr('_t');
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
        if (type == 2) {
            var water_count = $($('td.water_count').find('input').get(0)).val();
            if (!water_count || (water_count == 0) || water_count == '') {
                alert('请输入亲水值！');
                return false;
            }
            var send_water = $($('td.send_water').find('input').get(0)).val();
            if (!send_water || (send_water == 0) || send_water == '') {
                alert('请输入护水值！');
                return false;
            }
            var image_url_real1 = $($('td.image_url_real1').find('input').get(0)).val();
            if (!image_url_real1) {
                alert('请上传营业执照！');
                return false;
            }
            var image_url_real2 = $($('td.image_url_real2').find('input').get(0)).val();
            var image_url_real3 = $($('td.image_url_real3').find('input').get(0)).val();
            if (!(image_url_real2 || image_url_real3)) {
                alert('请至少上传一张店铺实景！');
                return false;
            }
        }
        $('#edit_user_info').submit();
    });
    $('a[type="edit_user"]').click(function () {
        $('td.user_name span').hide();
        $('td.user_name input').show();
        $('td.image_url img').hide();
        $('td.image_url input').show();
        var type = $(this).attr('_t');
        if (type == 2) {
            $('td.water_count span').hide();
            $('td.water_count input').show();
            $('td.send_water span').hide();
            $('td.send_water input').show();
            $('td.user_desc span').hide();
            $('td.user_desc textarea').show();
            $('td.image_url_real1 img').hide();
            $('td.image_url_real1 input').show();
            $('td.image_url_real2 img').hide();
            $('td.image_url_real2 input').show();
            $('td.image_url_real3 img').hide();
            $('td.image_url_real3 input').show();
        }
        $(this).hide();
    });
};