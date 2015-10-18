var admin_activity_manage = function (url) {
    this.url = url;
    this.init();
};
admin_activity_manage.prototype.init = function () {
    var that = this;
    $('td a').each(function () {
        $(this).click(function () {
            var id = $(this).attr('id');
            var status = $(this).attr('status');
            $.ajax({
                url: that.url,
                type: 'post',
                data: {id: id, status: status},
                dataType: 'json',
                success: function (res) {

                }
            });
        });
    });
};