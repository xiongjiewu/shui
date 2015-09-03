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
};