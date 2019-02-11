
$(document).ready(function () {
    // allow only  Alphabets A-Z a-z _ and space
    $('.alphaonly').bind('keyup blur',function(){ 
        var node = $(this);
//        node.val(node.val().replace(/[^A-Za-z_\s]/,'') ); 
        }   // (/[^a-z]/g,''
    );
    
    $('html').on('click', '.sbmKit', function () {
        var kitName = $('#kitName').val().trim();
        if (kitName == '') {
            $('#kitName').val('');
            $('#kitName').focus();
            alert('Please enter kit name.');
            return false;
        }
        var arrKitName = [kitName];
        $('.loader').show();
        $.ajax({
            url: SITE_URL + '/beautician/profile/saveBeauticianProfileKit',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            data: {kitName: arrKitName},
            success: function (response) {
                $('.loader').hide();
                if (response.success == true) {
                    $('#kitName').val('');
                    alert(response.message);
                    var list = '';
                    $.each(response.data, function (key, value) {
                        list += '<li><span class="icon icon-delete"></span>' + toTitleCase(value.kitName) + '<input type="hidden" name="kitNameId" value="' + value.id + '" /></li>';
                    });
                    $('.kitNameList').html('');
                    $('.kitNameList').html(list);
                } else {
                    alert(response.message);
                }
            },
            error: function (response) {
                $('.loader').hide();
                if (response.success == false) {
                    alert(response.message);
                } else {
                    checkLoginStatus(response.statusCode);
                }
            },
        });
    });

    
    $('html').on('click touchstart', '.icon-delete', function () {
        var kitId = $(this).siblings('input[name="kitNameId"]').val();
        $('.loader').show();
        var arrKitId = [kitId];
        $.ajax({
            url: SITE_URL + '/beautician/profile/deleteBeauticianKit',
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            data: {deletedKitId: arrKitId},
            currentElementScope: this,
            success: function (response) {
                $('.loader').hide();
                alert(response.message);
                if (response.success == true) {
                    $(this.currentElementScope).closest('li').remove();
                }
            },
            error: function (response) {
                $('.loader').hide();
                if (response.success == false) {
                    alert(response.message);
                } else {
                    checkLoginStatus(response.statusCode);
                }
            }
        });

    });

});