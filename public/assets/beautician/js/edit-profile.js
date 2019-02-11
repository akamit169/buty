//edit profile pic
$(function ($) {
    var options = {
        maximize: Infinity,
        onChange: valChanged,
        onMinimum: function (e) {
            console.log('reached minimum: ' + e)
        },
        onMaximize: function (e) {
            console.log('reached maximize' + e)
        }
    }
    $('#handleCounter').handleCounter(options)
})

function valChanged(d) {};
$(function () {
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            var imageElem = document.getElementById("pfImg");
            reader.onload = function (e) {
                imageElem.setAttribute('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#profile-img").change(function () {
        readURL(this);
    });
    $('html').on('click', '#radiusCheck', function () {
        if ($(this).is(':checked') == true) {
            $('.radius-div').removeClass('disable-click');
            $('#handleCounter input').prop('disabled',false);
        } else {
            $('.radius-div').addClass('disable-click');
            $('#handleCounter input').prop('disabled',true);
        }
    });
})
$(document).ready(function () {
    $.validator.addMethod("equalTo", function (value, element, param) {
        if (value == $('input[name="password"]').val()) {
            return true;
        }
        return false;
    }, 'Password and Confirm Password should be same.');
    $.validator.addMethod("validPassword", function (value, element, param) {
        var str = /(?=.*[a-zA-Z0-9])(?=.*[!$#%@])/;
        return str.test(value);
    }, 'Password should be minimum 4 characters and maximum 12 characters containing at least one special character (!, $, #,%,@).');
    var beauticianDetailProfile = $('#beauticianDetailProfile');
    beauticianDetailProfile.validate({
        onkeyup: false,
        onfocusout: false,
        rules: {
            suburb: {
            
            },
            country: {
                //                    required: true
            },
            phone: {
                //                    required: true,
                //                    number: true
            },
            instaId: {
                //                    required: true
            }
        },

        messages: {

        },
        submitHandler: function (form) {
            var result = 0;
            if ($('#radiusCheck').is(':checked') == false) {
                $('#workRadius option:selected').val(0);
            }
            $.ajax({
                url: SITE_URL + '/beautician/setting/latLongByAddress',
                type: 'GET',
                async: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data: {
                    address: $('#address').val(),
                    suburb: $('#suburb').val(),
                    country: $('#country').val()
                },
                success: function (response) {
                    $('#lat').val('');
                    $('#lng').val('');
                    if (response.success == true) {
                        $('#lat').val(response.data['lat']);
                        $('#lng').val(response.data['long']);
                        result = 1;
                    } else {
                        alert('- Please enter correct address');
                        result = 0;
                        return false;
                    }

                    return true;
                },
                error: function (response) {
                    result = 0;
                    $('#lat').val('');
                    $('#lng').val('');
                    var msg = '';
                    var response = JSON.parse(response.responseText);
                    $.each(response, function (index, value) {
                        msg += '- ' + value + '\n';
                    });
                    alert(msg);
                    return false;
                }
            });
            if (result > 0) {
                return true;
            }
            return false;
        },
        showErrors: function (error, element) {
            this.defaultShowErrors();
            $('.loading-overpay').hide();
        }
    });
    var changePasswordForm = $('#changePassword');
    changePasswordForm.validate({
        rules: {
            oldPassword: {
                //                    required: true,
                //                    minlength:4,
                //                    validPassword: true
            },
            password: {
                //                    required: true,
                //                    minlength:4,
                //                    validPassword: true
            },
            confirmPassword: {
                //                    required: true,
                //                    equalTo: true,
                //                    minlength:4,
                //                    validPassword: true
            }
        },

        messages: {

        },
        submitHandler: function (form) {

            $('.loading-overpay').show();
            $.ajax({
                url: SITE_URL + '/beautician/change-password',
                type: 'POST',
                async: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data: {
                    oldPassword: $('input[name="oldPassword"]').val(),
                    password: $('input[name="password"]').val(),
                    confirmPassword: $('input[name="confirmPassword"]').val()
                },
                success: function (response) {
                    $('.loading-overpay').hide();
                    if (response.success == true) {
                        $('input[name="oldPassword"]').val('');
                        $('input[name="password"]').val('');
                        $('input[name="confirmPassword"]').val('');
                        $('a.modal-close').trigger('click');
                    }
                    alert(response.message);
                    return false;
                },
                error: function (response) {
                    $('.loading-overpay').hide();
                    var msg = '';
                    var response = JSON.parse(response.responseText);
                    $.each(response, function (index, value) {
                        msg += '- ' + value + '\n';
                    });
                    alert(response.message);
                    return false;
                }
            });

            return false;
        },
        showErrors: function (error, element) {
            this.defaultShowErrors();
            $('.loading-overpay').hide();
        }
    });
});
//enable,disble travel radius

$(".radius-check").click(function () {
    if ($(this).is(":checked")) {
        $(".radius-div").removeClass('disable-click');
    } else {
        $(".radius-div").addClass('disable-click');
    }
});