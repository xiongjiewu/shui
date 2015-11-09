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
        if (!$('td.user_cellphone').has('input').length) {
            alert('编辑后方可保存');
            return false;
        }
        var user_cellphone = $('td.user_cellphone input').val();
        if (!user_cellphone) {
            alert('手机不能为空！');
            return false;
        }
        var user_name = $($('td.user_name').find('input').get(0)).val();
        if (!user_name) {
            alert('昵称不能为空！');
            return false;
        }
        var image_url = $($('td.image_url').find('input').get(0)).val();
        var water_count = $($('td.water_count').find('input').get(0)).val();
        if (water_count == undefined || water_count == '') {
            alert('亲水值不能为空！');
            return false;
        }
        var send_water = $($('td.send_water').find('input').get(0)).val();
        var black_water = $($('td.black_water').find('input').get(0)).val();
        var user_desc = $($('td.user_desc').find('input').get(0)).val();
        $.ajax({
            url: '/admin/business/update/' + user_id,
            type: 'put',
            data: {
                user_id: user_id,
                user_cellphone: user_cellphone,
                user_name: user_name,
                image_url: image_url,
                water_count: water_count,
                send_water: send_water,
                black_water: black_water,
                user_desc: user_desc
            },
            dataType: 'json',
            success: function (res) {

            }
        });
    });
    $('a[type="edit_user"]').click(function () {
        $('td.user_cellphone,td.user_name,td.image_url,td.water_count,td.send_water,td.black_water,td.user_desc').each(function () {
            $(this).html('<input type="text" value="' + $(this).html() + '" size="10">');
        });
        $(this).hide();
    });
};