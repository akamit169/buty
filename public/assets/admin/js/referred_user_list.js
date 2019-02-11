var userService = {};
$(document).ready(function () {
    var datatable;
    datatable = $('#basicDataTable').DataTable({
        "pagingType": "full_numbers",
        "serverSide": true,
        "paging": true,
        "ordering": false,
        "info": false,
        "ajax": SITE_URL + '/admin/referred-user-list-ajax',
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
                   if (data[4] != '' && data[4] != null) {
                        return data[4];
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 3,
                data: null,
                render: function (data, type, row) {
                    if(data[5]!= '') {
                        return data[5];
                    } else {
                        return '';
                    }
                }
            },
             {
                targets: 4,
                data: null,
                render: function (data, type, row) {
                    if(data[6]!= '') {
                        return data[6];
                    } else {
                        return '';
                    }
                }
            },
             {
                targets: 5,
                data: null,
                render: function (data, type, row) {
                    if(data[7]!= '') {
                        return data[7];
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 6,
                data: null,
                render: function (data, type, row) {
                    if(data[8]!= '') {
                        return "<div style='text-align:center;'>"+data[8]+"</div>";
                    } else {
                        return 'N/A';
                    }
                }
            },
            {
                targets: 7,
                data: null,
                render: function (data, type, row) {
                    var action_html = '';
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