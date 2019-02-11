$(document).ready(function () {
    $('#lightgallery').lightGallery({
        loop: false,
        controls: true
    });
});


$(function () {
    $('#cancel-booking').on('click', function () {

        var $this = $(this);
        var cancel = confirm("Are you sure you want to cancel this booking ?");
        if (cancel) {
            var bookingId = $('.inner-container').data("booking-id");

            $('.loader').show();

            $.ajax({
                type: 'POST',
                dataType: "json",
                url: SITE_URL + "/beautician/cancelBooking",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data: {
                    "bookingId": bookingId
                },
                success: function (response) {
                    $('.loader').hide();
                    alert(response.message);
                    if (response.success) {
                        $this.remove();
                        $('.b-status').addClass('red-text').text("Cancelled");
                    }
                },
                error: function (response) {
                    $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    checkLoginStatus(response.statusCode);
                    $('.ajax-loader').hide();

                }
            });
        }

    });
    $('html').on('change', '#profile-img', function () {
        $('form#image-share').submit();
    });

    var phHeight = $(".share-pic img").height() - 20;
    $(".share-btn").height(phHeight);

    var imgLength = $(".up-book-pic").children("img").length;
    $(".b-pics li").click(function () {
        if (imgLength == 0) {
            $(".lg-backdrop").hide();
            $(".lg-outer").hide();
        }
    });
});