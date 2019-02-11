

$(document).ready(function () {

    jQuery.fn.dataTableExt.oSort['string-case-asc'] = function (x, y) {
        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['string-case-desc'] = function (x, y) {
        return ((x < y) ? 1 : ((x > y) ? -1 : 0));
    };

    var datatable;


    
    datatable = $('#basicDataTable').DataTable({
  
        "pagingType": "full_numbers",
        "iDisplayLength": 25,
        "lengthMenu": [25, 50, 100],
        "serverSide": true,
        "paging": true,
        
        "info": false,

        "processing": true,
        "aaSorting": [[ 7, "asc"]],
         "columnDefs": [
            {
                "targets": [7],
                "visible": false,
               
            }
            
        ],
        "ajax": SITE_URL + '/admin/user/list-ajax',
        search: {
            caseInsensitive: true
        },
        columns: [
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email','searchable':false},
            {data: 'user_unique_id', name: 'user_unique_id','searchable':false},
            {data: 'phone', name: 'phone','searchable':false},
            {data: 'avg_rate', name: 'avg_rate','searchable':false},
            {data: 'status', name: 'status','searchable':false},
            {data: 'action', name: 'action', 'bSortable': false},
            {data: 'order', name: 'order','searchable':false,'bSortable': false},
        ]
         
    });

    
     $("#export-user-data").click(function(e) {
     
      var url=SITE_URL + '/admin/user/export-report';
      e.preventDefault();
      sendAjaxcall(url);  
     
     
    });
    
    

    //hide error after 3 seconds
    setTimeout(function () {
        $('.alert-danger').fadeOut('fast');
        $('.alert-success').fadeOut('fast');
    }, 3000);
});

function sendAjaxcall(url) {
    
    location.href = url;
    return;
 

}