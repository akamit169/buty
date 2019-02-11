var userService = {};
var displayText = {"travel_cost" : "Travel cost per km ($)","travel_time_km":"Travel time per km (minutes)"}
$(document).ready(function () {
    var datatable;
    datatable = $('#basicDataTable').DataTable({
        "paging": false,
        "serverSide": true,
        "ordering": false,
        "searching":false,
        "info": false,
        "ajax": SITE_URL + '/admin/app-settings-list-ajax',
        "columnDefs": [
            {
                targets: 0,
                data: null,
                render: function (data, type, row) {
                    if (data[0] != '' && data[0] != null) {
                        return displayText[data[0]];
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 1,
                data: null,
                render: function (data, type, row) {
                    if (data[1] != '' && data[1] != null) {
                        return '<span class="val-txt">'+data[1]+'</span><input data-id="'+data[2]+'" class="hide val-input" type="number" min=0 max=15 value="'+data[1]+'"/>';
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 2,
                data: null,
                render: function (data, type, row) {
                    var action_html = '<a class="btn btn-sm btn-default edit" href="#">Edit</a>';
                        action_html += '<a class="btn btn-sm btn-default save-config hide" href="#">Save</a>';
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
function sendAjaxcall(url) {
    location.href = url;
    return;
}

$(document).on('click','.edit',function(e){
    e.preventDefault();
    var row = $(this).closest('tr');
    row.find(".val-txt").addClass('hide');
    row.find(".val-input").removeClass('hide').focus();

    $(this).addClass('hide');
    $(this).next().removeClass('hide');
});

function saveConfigValue(inputElem)
{
        var $this = inputElem;
        var inputVal = $this.val();
        if(inputVal == "")
        {
            $this.val($this.prev('.val-txt').text());
            return;
        }
        else
        {
          var id = $this.data("id");
          $.ajax({
                type: 'POST',
                dataType:"json",
                url: SITE_URL+"/admin/modify-setting",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data:{"id":id,"value":inputVal},
                success: function (response) {
                    $('.loader').hide();
                    if(response.success == false)
                    {
                      alert(response.message);
                    }
                    else
                    {
                      $this.addClass('hide');
                      $this.prev('.val-txt').text(inputVal).removeClass('hide');
                    }
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    $('.ajax-loader').hide();
                    alert(response.message);
                    
                }
        });

        }
}


$(document).on('keydown','.val-input',function(e){
    if(e.which == 13)
    {
        saveConfigValue($(this));
    }
    
});

$(document).on('click','.save-config',function(e){
    var inputElem = $(this).closest('tr').find('.val-input');
    saveConfigValue(inputElem);

    $(this).addClass('hide');
    $(this).prev().removeClass('hide');
});


$(document).on('focusout','.val-input',function(e){
    var inputVal = $(this).val();
    if(inputVal == "")
    {
        $(this).val($(this).prev('.val-txt').text());
        return;
    }
    else
    {
      $(this).addClass('hide');
      $(this).prev('.val-txt').text(inputVal).removeClass('hide');
    }
    
});


