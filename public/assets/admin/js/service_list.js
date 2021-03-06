var userService = {};
$(document).ready(function () {
    var datatable;
    datatable = $('#basicDataTable').DataTable({
        "pagingType": "full_numbers",
        "serverSide": true,
        "paging": true,
        "ordering": false,
        "info": false,
        "ajax": SITE_URL + '/admin/service-booking/booked-service-list-ajax',
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
                    if (data[9] != '' && data[9] != null) {
                        return data[9];
                    } else {
                        return '';
                    }
                }
            }
        ]
    });
    
    $('html').on('change', '#bookingStatus', function(){
        var bookingStatus = $('#bookingStatus option:selected').val();
        var ajaxUrl = '';
        if(bookingStatus != '') {
            ajaxUrl = SITE_URL + '/admin/service-booking/booked-service-list-ajax?bookingStatus='+bookingStatus;
        } else {
            ajaxUrl = SITE_URL + '/admin/service-booking/booked-service-list-ajax';
        }
        $("#basicDataTable").dataTable().fnDestroy();
        var datatable;
        datatable = $('#basicDataTable').DataTable({
        "pagingType": "full_numbers",
        "serverSide": true,
        "paging": true,
        "ordering": false,
        "info": false,
        "ajax": ajaxUrl,
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
                    if (data[9] != '' && data[9] != null) {
                        return data[9];
                    } else {
                        return '';
                    }
                }
            }
        ]
    });
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