var userService = {};
$(document).ready(function () {
    var datatable;
    datatable = $('#basicDataTable').DataTable({
        "pagingType": "full_numbers",
        "serverSide": true,
        "paging": true,
        "ordering": false,
        "info": false,
        "ajax": SITE_URL + '/admin/beautician/beautician-list-ajax',
        "columnDefs": [
            {
                targets: 0,
                data: null,
                render: function (data, type, row) {
                    if (data[1] != '' && data[1] != null) {
                        return data[1];
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 1,
                data: null,
                render: function (data, type, row) {
                    if (data[2] != '' && data[2] != null) {
                        return data[2];
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 2,
                data: null,
                render: function (data, type, row) {
                    var action_html = '<a class="btn btn-sm btn-default view-log" href="'+SITE_URL+'/admin/beautician/approve-beautician/'+data[0]+'">Approve</a>';
                    action_html += '<a class="btn btn-sm btn-default view-log" href="'+SITE_URL+'/admin/beautician/reject-beautician/'+data[0]+'">Reject</a>';
                    action_html += '<a class="btn btn-sm btn-default view-log" href="'+SITE_URL+'/admin/beautician/view-beautician/'+data[0]+'">View Detail</a>';
                    return action_html;
                }
            }
        ]
    });
    $("#export-user-data").click(function(e) {
        var url=SITE_URL + '/admin/user/export-beautician/0';
        e.preventDefault();
        sendAjaxcall(url); 
    });
});
if($('.alert').length > 0) {
    setInterval(function() {
        $('.alert').hide();
    }, 2000);
}
function sendAjaxcall(url) {
    location.href = url;
    return;
}