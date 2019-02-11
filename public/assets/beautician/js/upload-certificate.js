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

/* submit the signup form   */
$('#submit_signup').click(function () {
    if(!$('#tcCheck').is(":checked"))
    {
        alert("Please accept the terms and conditions");
        return false;
    }

    $signup_action = $('#signupForm').attr('action');
    var newdat = new FormData($('#signupForm')[0]);
    $.ajax({
        url: $signup_action,
        type: 'post',
        data: newdat,
        contentType: false,
        cache: false,
        processData: false,
        headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
        success: function (result) {
            if (result.success) {
                window.location.href = result.redirectUrl;
            } else {
                showValidationError(result.message);
            }
        },
        error: function (errors) {
            $.each(errors.responseJSON, function (index, element) {
                showValidationError(element[0]);
                return false;
            });
        }
    });

});
