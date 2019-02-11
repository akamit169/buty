
$(document).ready(function () {

    jQuery.fn.dataTableExt.oSort['string-case-asc'] = function (x, y) {
        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['string-case-desc'] = function (x, y) {
        return ((x < y) ? 1 : ((x > y) ? -1 : 0));
    };

  
    var datatable;
    var userId=$('#user-id').val();
    datatable = $('#basicDataTable').DataTable({
       
        "pagingType": "full_numbers",
        "iDisplayLength": 25,
        "lengthMenu": [25, 50, 100],
        "serverSide": true,
        "paging": true,

        "info": false,
        "aaSorting": [[ 5, "desc"]],
        "processing": true,
        "ajax": SITE_URL + '/admin/user/feedback-list-ajax/'+userId,
        
        
        search: {
            caseInsensitive: true
        },
     
        columns: [
            {data: 'sender_name', name: 'sender_name'},
            {data: 'sender_email', name: 'sender_email'},
            {data: 'user_unique_id', name: 'user_unique_id'},
            {data: 'rating', name: 'rating','searchable':false},
            {data: 'skills', name: 'skills','searchable':false},
             {
                "render": function (data, type, full, meta) {
                    return convertToLocalDateTime(full.created_at);
                }
            },

        ]
    });
    
    
    function convertToLocalDateTime(utc_datetime)
    {
        var date = Date.parse(utc_datetime) || 0;

        if (utc_datetime != '0000-00-00 00:00:00')
        {

            var supported_format_updated_at = utc_datetime.replace(/-/g, "/");
            var updated_at_UTC = new Date(supported_format_updated_at + " UTC");
            var updated_at_localdatetime = updated_at_UTC.toString();
            var date = moment(updated_at_localdatetime).format('MM-DD-YYYY');
            var time = moment(updated_at_localdatetime).format('h:mm A');
            return date + ' at ' + time;
        }
        return '';
       
    }



    //hide error after 3 seconds
    setTimeout(function () {
        $('.alert-danger').fadeOut('fast');
        $('.alert-success').fadeOut('fast');
    }, 3000);
});