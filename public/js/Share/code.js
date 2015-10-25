var share_get = function (code) {
    this.code = code;
    this.init();
};
share_get.prototype.init = function () {
    var that = this;
    $('.sure-button').click(function () {
        var phone = $('input[name="phone"]').val();
        if (!phone) {
            alert('请输入手机!');
            return false;
        }

        if (phone.length != 11) {
            alert('请输入正确手机!');
            return false;
        }

        var code = $('input[name="code"]').val();
        if (!code) {
            alert('请输入验证码!');
            return false;
        }
        $.ajax({
            url: '/shareGet',
            type: 'post',
            data: {code: that.code, cellphone: phone, verify: code},
            dataType: 'json',
            success: function (res) {
                if (res.status || (res.code == 4)) {
                    $('.content1').hide();
                    $('.account').html(phone);
                    $('.content2').show();
                    $('.title-show').hide();
                    $('.qingshuizhi-show').html(res.info.water_count).show();
                    $('.show-account').show();
                    that.show_list();
                    return true;
                } else {
                    $('.content1').hide();
                    $('.account').html(phone);
                    $('.content2').show();
                    $('.title-show').html(res.message);
                    $('.qingshuizhi-show').html(res.info.water_count).show();
                    $('.show-account').show();
                    that.show_list();
                    return true;
                }
                return false;
            }
        });
    });
    $('.show-button').click(function () {
        var phone = $('input[name="phone"]').val();
        if (!phone) {
            alert('请输入手机!');
            return false;
        }

        if (phone.length != 11) {
            alert('请输入正确手机!');
            return false;
        }
        that.send_code(phone);
    });
};

share_get.prototype.send_code = function (phone) {
    $.ajax({
        url: '/verify',
        type: 'post',
        data: {cellphone: phone},
        dataType: 'json',
        success: function (res) {
            alert(res.message);
        }
    });
};

share_get.prototype.show_list = function () {
    var that = this;
    $.ajax({
        url: '/share/show/' + that.code + '.html',
        type: 'get',
        dataJson: 'json',
        success: function (res) {
            if (res.code == 0) {
                var html = '';
                $(res.userInfo).each(function (index, val) {
                        var c = (index == 0) ? 'first' : '';
                        html +=
                            '<tr class="' + c + '">' +
                            '<td class= "user-img" ><img src = "/image/touxiang_03.png" ></td>' +
                            '<td class= "user-info" ><div class = "time-name" ><span class= "name" >' + val.user_name + '</span><span class= "time" >' +
                            val.created_at + '</span></div><div class= "time-content" >此包只应天上有</div></td > ' +
                            '<td class= "user-water" >' + val.share_water_count + '</td></tr>';
                    }
                );
                $('.show-list table').html(html);
                $('.content3').show();
                return true;
            }
            return false;
        }
    });
};