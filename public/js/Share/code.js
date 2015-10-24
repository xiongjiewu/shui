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
                if (res.code == 0) {//成功
                    return true;
                }
                alert(res.message);
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