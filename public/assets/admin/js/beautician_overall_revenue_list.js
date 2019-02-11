var userService = {};
$(document).ready(function () {
    var datatable;
    datatable = $('#basicDataTable').DataTable({
        "pagingType": "full_numbers",
        "serverSide": true,
        "paging": true,
        "ordering": false,
        "info": false,
        "ajax": SITE_URL + '/admin/user/beautician-revenue-list-ajax',
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
                        return '-';
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
                        return '-';
                    }
                }
            }
        ]
    });
    
    $('html').on('click', '#customSearch', function() {
        
        if($('#suburb').val() == '' && ($('#month').val() == '' || $('#year').val() == '')) {
            alert('Please select month as well as year.');
            return false;
        }
        var url = SITE_URL + '/admin/user/beautician-revenue-list-ajax';
        if($('#suburb').val() != '') {
            url += '?suburb='+$('#suburb').val();
        }
        if($('#month').val() != '' && $('#year').val() != '' && $('#suburb').val() == '') {
            url += '?month='+$('#month').val()+'&year='+$('#year').val();
        } else if($('#month').val() != '' && $('#year').val() != '' && $('#suburb').val() != '') {
            url += '&month='+$('#month').val()+'&year='+$('#year').val();
        }
        $("#basicDataTable").dataTable().fnDestroy();
        var datatable;
        datatable = $('#basicDataTable').DataTable({
        "pagingType": "full_numbers",
        "serverSide": true,
        "paging": true,
        "ordering": false,
        "info": false,
        "ajax": url,
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
                        return '-';
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
                        return '-';
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