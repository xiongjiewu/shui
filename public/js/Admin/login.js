var admin_login = function (login_url, to_url) {
    this.login_url = login_url;
    this.to_url = to_url;
    this.init();
};

admin_login.prototype.init = function () {
    var that = this;
    $("#admin_login").submit(function () {
        that.login($.trim($('#user_name').val()), $.trim($('#password').val()))
    });
};
admin_login.prototype.login = function (user_name, password) {
    var that = this;
    $.ajax({
        url: that.login_url,
        type: 'post',
        data: {'user_name': user_name, 'password': password},
        dataType: 'json',
        success: function (res) {
            if (res.status) {
                window.location.href = that.to_url;
            } else {
                alert(res.msg);
            }
        }
    });
};