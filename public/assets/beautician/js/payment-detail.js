$('#bankDOB').datepicker({dateFormat: 'dd/mm/yy', maxDate: 0});
// Create a token or display an error when the form is submitted.
var form = document.getElementById('beauticianPaymentDetail');
$(function () {
    var $form = $('#beauticianPaymentDetail');
    $form.submit(function (event) {
        event.preventDefault();

        // Disable the submit button to prevent repeated clicks:
        $form.find('#sbmBtn').prop('disabled', true);
        if ($('#cardNumber').val() == '' || 
                $('input[name="accountNo"]').val() == '' || $('input[name="accountNo"]').val().length < 4 || $('#cardName').val == ''
                || $('#expiryMonth').val() == '' || $('#expiryYear').val() == '' || $('input[name="bankFirstName"]').val() == '' ||
                $('input[name="bankLastName"]').val() == '' || $('#accountVerifyDoc').val() == '')  {
            alert('- Please enter all the details to proceed further.');
            $('#beauticianPaymentDetail').find('#sbmBtn').prop('disabled', false);
            return false;
        }
        // Request a token from Stripe:
        if ($('#cvc').val() != '' && !isNaN($('#cardNumber').val())) {
            Stripe.card.createToken($form, stripeResponseHandler);
        } else if ($('input[name="accountNo"]').val() != '' && $('input[name="accountNo"]').val().length > 4) {
            var msg = '';
            if ($('input[name="bankFirstName"]').val() == '') {
                msg += '- Please Enter First Name\n';
            }
            if ($('input[name="bankLastName"]').val() == '') {
                msg += '- Please Enter Last Name\n';
            }
            if ($('input[name="dob"]').val() == '') {
                msg += '- Please Enter Date of Birth \n';
            }
            else
            {
                var inputDate = $('input[name="dob"]').val();
                if(!moment(inputDate, 'D-M-YYYY', true).isValid())
                {
                   msg += '- Please Enter a valid Date of Birth \n';
                }
                else
                {
                    var years = moment().diff(moment(inputDate, "D-M-YYYY"), 'years');
                    if(years < 13)
                    {
                      msg += '- Your current age should be atleast 13 years \n';
                    }
                    

                }

            }

            if ($('input[name="bsb"]').val() == '') {
                msg += '- Please Enter BSB Number\n';
            }
            if ($('#accountVerifyDoc').val() == '') {
                msg += '- Please upload verification document\n';
            }
            if (msg == '') {
                var form = document.getElementById('beauticianPaymentDetail');
                form.submit();
            } else {
                alert(msg);
            }
        } else {
            alert('- Please enter either bank or card detail to proceed further.');
        }

        $('#beauticianPaymentDetail').find('#sbmBtn').prop('disabled', false);
        // Prevent the form from being submitted:
        return false;
    });
});

function stripeResponseHandler(statusCode, response) {
    if (response.error) {
        // Show the errors on the form
        var errorHtml = '<div class="alert alert-danger alert-dismissable server-error success-msg-div">\n\
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4>\n\
    <i class="icon fa fa-ban"></i> Error !</h4><label class="error-msg">* ' + response.error.message + '</label><br/></div>';
        $(errorHtml).insertAfter('#beauticianPaymentDetail');
        return false;
    } else {
        // Insert the token ID into the form so it gets submitted to the server
        var form = document.getElementById('beauticianPaymentDetail');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'cardToken');
        hiddenInput.setAttribute('value', response.id);
        form.appendChild(hiddenInput);
        // Submit the form
        form.submit();
    }

}

$("#bsb").keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                            (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
$('input[name="accountNo"]').keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                            (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
$("#expiryMonth").keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                            (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
$("#expiryYear").keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                            (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
$("#cvc").keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                            (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
$("#cardNumber").keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                            (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

/* upload police verification certificate  */
$(function () {
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            var imageElem = document.createElement("img");
            reader.onload = function (e) {
                imageElem.setAttribute('src', e.target.result);
                $('.uploaded-certificate').append(imageElem);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#profile-img").change(function () {
        readURL(this);
        $('.police-certificate').show();
        $('.upload-btn-wrap').hide();
//            $(this).val('');
    });

    $('html').on('click touchstart', '.cross-icon', function () {
        $('.uploaded-certificate').find('img').remove();
        $('.police-certificate').hide();
        $('.upload-btn-wrap').fadeIn();
        $('#profile-img').val("");
    });

});