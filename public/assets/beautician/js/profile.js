$('.text-area textarea').on('keyup',function(){
       var charLength = $(this).val().length;

       $(this).parent().find('.char-count').text(charLength);

});


//lightbox intialization
$(document).ready(function () {


    function getBeauticianPortfolioByService(serviceId, elem) {
        $.ajax({
            url: SITE_URL + '/beautician/getBeauticianPortfolioByService?serviceId=' + serviceId,
            type: 'GET',
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function (response) {
                populatePortfolioByService(response.data, elem);

            },
            error: function (response) {
                var response = JSON.parse(response.responseText);
                if (response.success == false) {
                    alert(response.message);
                } else {
                    checkLoginStatus(response.statusCode);
                }
            },
        });
    }

    if(localStorage.uploadedPortofolioServiceId)
    {
        var uploadedServiceId = localStorage.uploadedPortofolioServiceId;
        var serviceElem = $('.main-service[data-service-id='+uploadedServiceId+']');
        $(serviceElem).find('a').trigger('click');
        getBeauticianPortfolioByService(serviceElem.attr('data-service-id'), $(serviceElem.find('a').attr('href')));
        localStorage.uploadedPortofolioServiceId = "";
    }
    else
    {
        var firstService = $('.main-service').first();
        getBeauticianPortfolioByService(firstService.attr('data-service-id'), $(firstService.find('a').attr('href')));

    }

    $('#edit-popup').modal({
        ready: function (modal, trigger) { // Callback for Modal open. Modal and trigger parameters available.
            var str = $('#business_description').html().trim();
            str = str.replace(/(?:\n)/g, '');  //remove new lines
            str = str.replace(/(?:<br>|<br \/>)/g, '\n'); //replace br with \n
            $('#edit-popup textarea').val(str);

            $('.text-area').find('.char-count').text(str.length);
        }
    });

    $('#save-description').on('click', function () {
        $.ajax({
            url: SITE_URL + '/beautician/updateBusinessDescription',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            data: {
                "businessDescription": $('#edit-popup textarea').val()
            },
            success: function (response) {
                if (response.success == true) {
                    $('#edit-popup').modal('close');
                    var str = $('#edit-popup textarea').val();
                    str = str.replace(/(?:\r\n|\n)/g, '<br />');
                    $('#business_description').html(str);

                } else {
                    alert(response.message);
                }

            },
            error: function (response) {
                var response = JSON.parse(response.responseText);
                if (response.success == false) {
                    alert(response.message);
                } else {
                    checkLoginStatus(response.statusCode);
                }
                $('.loader').hide();
            },
        });
    });


    $('.main-service a').on('click', function () {
        var serviceId = $(this).closest('li').attr('data-service-id');
        var elem = $(this).attr('href');
        getBeauticianPortfolioByService(serviceId, $(elem));
    });


    $(document).on('click', '.delete-portfolio', function () {
        $("body").removeClass("lg-on");
        var portfolioId = $(this).attr('data-portofolio-id');
        $(this).closest('li').remove();

        $.ajax({
            url: SITE_URL + '/beautician/beauticianPortfolio?portfolioId=' + portfolioId,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function (response) {
                if (response.success == false) {
                    alert(response.message);
               }
            },
            error: function (response) {
                var response = JSON.parse(response.responseText);
                if (response.success == false) {
                    alert(response.message);
                } else {
                    checkLoginStatus(response.statusCode);
                }
            },
        });
        $(".lg-outer").hide();
        $(".lg-backdrop").hide();
        
    });



    function populatePortfolioByService(data, elem) {
        var html = "";
        $.each(data, function (key, val) {
            html += '<li data-responsive="' + val.image + '" data-src="' + val.image + '">' +
                '<div class="caption-box">' +
                '<img src="' + val.image + '">' +
                '<span data-portofolio-id="' + val.portfolioId + '" class="delete-caption icon icon-delete delete-portfolio"></span>' +
                '</div>' +
                '</li>';
        });

        if (!html) {
            html = "<div style='padding:50px 0;font-size:20px;text-align:center; color:#f3f3f3;'>No Portfolio Uploaded</div>";
        } else {
            html = '<ul class="lightgallery">' + html + '</ul>';
        }

        elem.html(html);

        $('.lightgallery').lightGallery({
            loop: false,
            controls: true
        });

    }


});