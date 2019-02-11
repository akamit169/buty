var userService = {};
$(document).ready(function () {
    var datatable;
    datatable = $('#basicDataTable').DataTable({
        "pagingType": "full_numbers",
        "serverSide": true,
        "paging": true,
        "ordering": false,
        "info": false,
        "ajax": SITE_URL + '/admin/service-booking/disputed-service-list-ajax',
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
                    if (data[3] != '' && data[3] != null) {
                        return data[3];
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 3,
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
                targets: 4,
                data: null,
                render: function (data, type, row) {
                    if (data[5] != '' && data[5] != null) {
                        return moment.utc(data[5]).local().format('DD-MMM-YYYY h:mm A');
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 5,
                data: null,
                render: function (data, type, row) {
                    if (data[6] != '' && data[6] != null) {
                        return moment.utc(data[6]).local().format('DD-MMM-YYYY h:mm A');
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 6,
                data: null,
                render: function (data, type, row) {
                    if (data[7] != '' && data[7] != null) {
                        return data[7];
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 7,
                data: null,
                render: function (data, type, row) {
                    if (data[8] != '' && data[8] != null) {
                        return data[8];
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 8,
                data: null,
                render: function (data, type, row) {
                    if (data[10] != '' && data[10] != null) {
                        return data[10];
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 9,
                data: null,
                render: function (data, type, row) {
                     if (data[9] != '' && data[9] != null) {
                        return data[9];
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 10,
                data: null,
                render: function (data, type, row) {
                    var action_html = '<a class="btn btn-sm btn-default view-log resolve-dispute" data-booking-id="'+data[0]+'" href="'+SITE_URL+'/admin/service-booking/resolve-dispute/'+data[0]+'">Resolve</a> \n\
                                    <a class="btn btn-sm btn-default view-log reject-dispute" data-booking-id="'+data[0]+'" href="'+SITE_URL+'/admin/service-booking/reject-dispute/'+data[0]+'">Reject</a>';
                    return action_html;
                }
            }
        ]
    });
});


$(document).on('click','.resolve-dispute',function(e){
    e.preventDefault();
    var bookingId = $(this).data("booking-id");
    var confirmed = confirm("Are you sure you want to resolve this dispute ?");
    if(confirmed)
    {
         $.ajax({
                type: 'POST',
                dataType:"json",
                url: SITE_URL+"/admin/beautician/resolve-dispute",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data:{"bookingId":bookingId},
                success: function (response) {
                    $('.loader').hide();
                    alert(response.message);
                    location.reload();
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    $('.ajax-loader').hide();
                    
                }
        });
    }

});

$(document).on('click','.reject-dispute',function(e){
    e.preventDefault();
    var bookingId = $(this).data("booking-id");
    var confirmed = confirm("Are you sure you want to reject this dispute ?");
    if(confirmed)
    {
        $.ajax({
                type: 'POST',
                dataType:"json",
                url: SITE_URL+"/admin/beautician/reject-dispute",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data:{"bookingId":bookingId},
                success: function (response) {
                    $('.loader').hide();
                    alert(response.message);
                    if(response.success == true)
                    {
                     location.reload();
                    }
                   
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    $('.ajax-loader').hide();
                    
                }
        });
    }
})



if($('.alert').length > 0) {
    setInterval(function() {
        $('.alert').hide();
    }, 2000);
}
function sendAjaxcall(url) {
    location.href = url;
    return;
}