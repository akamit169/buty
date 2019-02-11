var userService = {};
$(document).ready(function () {
    var datatable;
    datatable = $('#basicDataTable').DataTable({
        "pagingType": "full_numbers",
        "serverSide": true,
        "paging": true,
        "ordering": false,
        "info": false,
        "ajax": SITE_URL + '/admin/user/suspended-user-list-ajax',
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
                    if(data[3] == 2) {
                        return 'Beauty Pro';
                    } else {
                        return 'Client';
                    }
                }
            },
            {
                targets: 3,
                data: null,
                render: function (data, type, row) {
                    var action_html = '<a class="btn btn-sm btn-default view-log" href="'+SITE_URL+'/admin/user/suspend-unsuspend-user/'+data[0]+'">Unsuspend</a>';
                    if(data[3] == 2) {
                        action_html += '<a class="btn btn-sm btn-default view-log" href="'+SITE_URL+'/admin/beautician/view-beautician/'+data[0]+'">View Detail</a>';
                    } else {
                        action_html += '<a class="btn btn-sm btn-default view-log" href="'+SITE_URL+'/admin/customer/view-customer/'+data[0]+'">View Detail</a>';
                    }
                    return action_html;
                }
            }
        ]
    });
});
if($('.alert').length > 0) {
    setInterval(function() {
        $('.alert').hide();
    }, 2000);
}