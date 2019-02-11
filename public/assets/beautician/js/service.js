/* mobile service */


$('textarea').on('keyup',function(){
       var charLength = $(this).val().length;

       $(this).parent().find('.char-count').text(charLength);

});

$('.service-checklist').slimScroll({
    alwaysVisible: true,
    railVisible: true,
    height: '180px',
});
$('html').on('click', '.main-services .tab', function () {
    $('.service-checklist').slimScroll({
        scrollTo: '0px'
    });
});

$('html').on('click', '.info-tip', function () {
    $("html, body").animate({
        scrollTop: $(document).height()
    }, 1000);
});

var subServicesDetailsArr = {};

$(function () {

    var form = $('#save-service');

    $("#datepicker").datepicker({
        dateFormat: 'D, d M yy',
        dayNamesMin: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'],
        minDate: 0
    }).datepicker("setDate", "0");

    $('#save-service').find('[name=discountStartDate]').val(convertToUTCDateTime($('#datepicker').val() + " 00:00:00"));

    $("#datepicker").click(function () {
        var inputWidth = $("#datepicker").width();
        $(".ui-datepicker-div").width("inputWidth");
    });

    $(".add-service").click(function () {
        $('.services-panel').show();
        resetForm();
        setFirstServiceAsSelected();
    });

    $('.select-session').on('change', function (e) {
        if ($('.select-session').is(':checked')) {
            $(".l-disable").css({
                "opacity": "1",
                "pointer-events": "inherit"
            });
        } else {
            $(".l-disable").css({
                "opacity": "0.4",
                "pointer-events": "none"
            });

            form.find('[name=sessionNumber]').val(1).material_select();
            form.find('[name=timeBtwSession]').val(1).material_select();
        }
    });
    $('.select-discount').on('change', function (e) {
        if ($('.select-discount').is(':checked')) {
            $(".r-disable").css({
                "opacity": "1",
                "pointer-events": "inherit"
            });
        } else {
            $(".r-disable").css({
                "opacity": "0.4",
                "pointer-events": "none"
            });

            form.find('[name=discount]').val('');
            form.find('[name=discountedDays]').val("");
            $('#datepicker').datepicker("setDate", "0");
            form.find('[name=discountStartDate]').val(convertToUTCDateTime($('#datepicker').val() + " 00:00:00"));
        }
    });



    function getSubServices(parentServiceId, isEdit) {
        $.ajax({
            url: SITE_URL + '/api/service/subServices?' + 'parentServiceIdArr[]=' + parentServiceId,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function (response) {
                createSubServicesHtml(response.subServices);
                if (isEdit) {
                    setEditFields();
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
    }

    function createSubServicesHtml(data) {
        var html = "";
        $.each(data, function (key, val) {

            subServicesDetailsArr[val.id] = {
                "description": val.description,
                "tip": val.tip
            };

            html += '<div class="checkboxGroup services-line">' +
                '<input id="service' + val.id + '" class="checks" type="radio" name="serviceId" value="' + val.id + '">' +
                '<label for="service' + val.id + '" class=""></label>' +
                '<span class="s-name">' + capitalizeTxt(val.name) + '</span>' +
                '<span class="tooltipped" data-position="top" data-delay="50" data-tooltip="Lorem Ipsum is simply dummy text of the printing simply dummy text of the printing and typesetting industry. "><i class="icon icon-info info-tip"></i></span>' +
                '</div>';
        });

        $('.service-checklist').html(html);
    }

    function capitalizeTxt(str) {
        return str.replace(/\w\S*/g, function (txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });
    }

    function setFirstServiceAsSelected()
    {
       var selectedService = $('.main-services').first().find('a').first();
       setSelectServiceActions(selectedService);
    }

    setFirstServiceAsSelected();

    $('.main-services a').on('click', function () {
        if ($('#save-service').find('[name=id]').val() !== "") {
            return false;
        }
        setSelectServiceActions($(this));
    });

    function setSelectServiceActions(elem, isEdit) {
        $('.main-services a').removeClass('active');
        getSubServices(elem.attr('data-id'), isEdit);
        elem.addClass('active');
        $('#save-service').find('[name="parentServiceId"]').val(elem.attr('data-id'));
    }



    $(document).on('click', '.added-service', function () {
        if ($(this).hasClass('selected')) {
            return false;
        }

        $('.added-service').removeClass('selected');
        $(this).addClass('selected');
        var parentServiceId = $(this).attr('data-parent-id');
        var elem = $('.main-services').find('a[data-id=' + parentServiceId + ']');
        $('.services-panel').show();
        setSelectServiceActions(elem, true);

        var dropdown = $(".mobile-inner-menu .dropdown-button");
        var activeService = $('#dropdown1').find('a.active').text();
        if(activeService)
        {
          dropdown.text(activeService);  
        }
    });

    function setEditFields() {
        var selectedService = $('.added-service.selected');
        form.find('[name=duration]').val(selectedService.data('duration')).material_select();
        form.find('[name=cost]').val(selectedService.data('cost'));
        form.find('[name=parentServiceId]').val(selectedService.data('parent-id'));
        form.find('[name=id]').val(selectedService.data('id'));
        form.find('[name=serviceId][value=' + selectedService.data('service-id') + ']').prop('checked', true);
        
        var description = form.find('[name=description]');
        var tip = form.find('[name=tip]');

        description.val(selectedService.data('description'));
        description.parent().find('.char-count').text(selectedService.data('description').length);

        tip.val(selectedService.data('tip'));
        tip.parent().find('.char-count').text(selectedService.data('tip').length);



        if (selectedService.data('sessions') > 1) {
            $('#rc1').trigger('click');
            form.find('[name=sessionNumber]').val(selectedService.data('sessions')).material_select();
            form.find('[name=timeBtwSession]').val(selectedService.data('time_btw_sessions')).material_select();
        } else {
            !$('#rc1').is(':checked') || $('#rc1').trigger('click');
            form.find('[name=sessionNumber]').val(1).material_select();
            form.find('[name=timeBtwSession]').val(1).material_select();
        }

        if (selectedService.data('discount') > 0) {
            $('#dis1').trigger('click');
            form.find('[name=discount]').val(selectedService.data('discount'));
            form.find('[name=discountedDays]').val(selectedService.data('discounted_days'));
            form.find('[name=discountStartDate]').val(selectedService.data('discount_startdate'));
            $('#datepicker').val(convertToLocalDateTime(selectedService.data('discount_startdate')));
        } else {
            !$('#dis1').is(':checked') || $('#dis1').trigger('click');
            form.find('[name=discount]').val('');
            form.find('[name=discountedDays]').val("");
            $('#datepicker').datepicker("setDate", "0");
            form.find('[name=discountStartDate]').val(convertToUTCDateTime($('#datepicker').val() + " 00:00:00"));

        }

        form.attr('action', SITE_URL + '/beautician/updateService');

    }

    $('#datepicker').change(function () {
        var localDate = $(this).val();
        var DateTimeYmd = convertToUTCDateTime(localDate + " 00:00:00");
        $('#save-service').find('[name=discountStartDate]').val(DateTimeYmd);
    });


    function convertToLocalDateTime(utcDatetime) {
        var supported_format_updated_at = utcDatetime.replace(/-/g, "/");
        var updated_at_UTC = new Date(supported_format_updated_at + " UTC");
        var updated_at_localdatetime = updated_at_UTC.toString();
        var date = moment(updated_at_localdatetime).format('ddd, D MMM YYYY');
        return date;
    }

    function convertToUTCDateTime(localDatetime) {
        var UtcYMD = moment(localDatetime, "ddd, D MMM YYYY HH:mm:ss").utc().format("YYYY-MM-DD HH:mm:ss");
        return UtcYMD;
    }

    function resetForm() {
        var form = $('#save-service');

        form.find('[name=duration]').val(15).material_select();
        form.find('[name=cost]').val('');
        form.find('[name=id]').val("");
        form.find('[name=serviceId]').prop('checked', false);


        !$('#rc1').is(':checked') || $('#rc1').trigger('click');
        form.find('[name=sessionNumber]').val(1).material_select();
        form.find('[name=timeBtwSession]').val(1).material_select();


        !$('#dis1').is(':checked') || $('#dis1').trigger('click');
        form.find('[name=discount]').val('');
        form.find('[name=discountedDays]').val("");
        $('#datepicker').datepicker("setDate", "0");
        form.find('[name=discountStartDate]').val(convertToUTCDateTime($('#datepicker').val() + " 00:00:00"));

        var description = form.find('[name=description]');
        var tip = form.find('[name=tip]');

        description.val('');
        tip.val('');

        description.parent().find('.char-count').text(0);
        tip.parent().find('.char-count').text(0);



        form.attr('action', SITE_URL + '/beautician/createService');
        $('.added-service').removeClass('selected');

    }

    $('html').on('click', '#save', function (e) {
        var form = $('#save-service');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function (response) {
                if (form.find('[name=id]').val() !== "") {
                    showValidationError(response.message);
                    updateSavedServiceHTML();
                } else {
                    location.reload();
                }
            },
            error: function (response) {
                var response = JSON.parse(response.responseText);
                var message = '';
                $.each(response, function (index, value) {
                    if (message == '') {
                        message = value;
                    }
                });
                if (message != '') {
                    showValidationError(message);
                } else {
                    checkLoginStatus(response.statusCode);
                }
            },
        });


        return false;


    });

    function round(value, exp) {
  if (typeof exp === 'undefined' || +exp === 0)
    return Math.round(value);

  value = +value;
  exp = +exp;

  if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0))
    return NaN;

  // Shift
  value = value.toString().split('e');
  value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp)));

  // Shift back
  value = value.toString().split('e');
  return +(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp));
}


    function updateSavedServiceHTML() {
        var selectedService = $('.added-service.selected');
        var form = $('#save-service');

     
        selectedService.find('.child-service-name').text($('#service' + form.find('[name=serviceId]:checked').val()).next().next().text());
        selectedService.find('.child-service-session').text(form.find('[name=sessionNumber]').val());
        selectedService.find('.child-service-duration').text(form.find('[name=duration]').val());

        var cost = parseFloat(form.find('[name=cost]').val());
        var discount = parseFloat(form.find('[name=discount]').val());
        if (discount) {
            selectedService.find('.child-service-discount').text(discount);
            selectedService.find('.discount-section').show();

            var discountedPrice = cost - (cost * discount / 100);
            var costHtml = '<p><span class="new-price">$<span class="child-service-cost">' + round(discountedPrice,2) + '</span></span><span class="old-price">$' + cost + '</span></p>' +
                '<div class="service-session">+ Travel cost</div><a href="#" class="del-services">delete</a>';


        } else {
            var costHtml = '<p>$<span class="child-service-cost">' + cost + '</span></p>' +
                '<div class="service-session">+ Travel cost</div><a href="#" class="del-services">delete</a>';

            selectedService.find('.discount-section').hide();
        }


        //update price section
        selectedService.find('.a-d-right').html(costHtml);

        //update data attr
        selectedService.data('service-id', form.find('[name=serviceId]').val());
        selectedService.data('duration', form.find('[name=duration]').val());
        selectedService.data('cost', form.find('[name=cost]').val());
        selectedService.data('description', form.find('[name=description]').val());
        selectedService.data('tip', form.find('[name=tip]').val());

        selectedService.data('sessions', form.find('[name=sessionNumber]').val());
        selectedService.data('time_btw_sessions', form.find('[name=timeBtwSession]').val());


        selectedService.data('discount', form.find('[name=discount]').val());
        selectedService.data('discount_startdate', form.find('[name=discountStartDate]').val());
        selectedService.data('discounted_days', form.find('[name=discountedDays]').val());


    }

    $('#cancel').on('click', function () {
        $('.services-panel').hide();
        resetForm();
    });

    var beauticianServiceDeletedId;

    $(document).on('click', '.del-services', function (e) {
        e.stopPropagation();
        beauticianServiceDeletedId = $(this).closest('.added-service').data('id');

        $('#alert-modal').modal('open');
    });


    $('#delete-ok').on('click', function () {

        $.ajax({
            url: SITE_URL + "/beautician/profile/deleteService?beauticianServiceId=" + beauticianServiceDeletedId+ "&timezone="+ moment.tz.guess(),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function (response) {
                location.reload();
            },
            error: function (response) {
                var response = JSON.parse(response.responseText);
                var message = '';

                $.each(response, function (index, value) {
                    if (message == '') {
                        message = value;
                    }
                });
                if (message != '') {
                    showValidationError(message);
                } else {
                    checkLoginStatus(response.statusCode);
                }
            },
        });

    });

    $(document).on('click', '.checkboxGroup', function () {
        if (form.find('[name=id]').val() == "") {
            var subserviceId = form.find('input[name=serviceId]').val();
            subserviceDetails = subServicesDetailsArr[subserviceId];
            form.find('[name=description]').val(subserviceDetails.description);
            form.find('[name=tip]').val(subserviceDetails.tip);
        }

    });
});


$(document).load($(window).bind("resize", listenWidthCh));

function listenWidthCh(e) {
    if ($(window).width() < 768) {
        $(".add-service a , .added-service").click(function () {
            $(".mobile-up").hide();
            $(".mobile-down").show();
            $(".back-arrow").show();
            $(".menu-icon").hide();
        });
        $(".back-arrow").click(function () {
            $(".mobile-up").show();
            $(".mobile-down").hide();
            $(".back-arrow").hide();
            $('.menu-icon').show();
        });

    } else {
        $(".mobile-up").show();
        $(".mobile-down").show();
    }
}
listenWidthCh();

$("html").on("click", ".mobile-inner-menu .dropdown-content a", function () {
    $(".mobile-inner-menu .dropdown-content").removeClass("active");
    $(".mobile-inner-menu .dropdown-button").removeClass("active");
    $(".mobile-inner-menu .dropdown-button").html($(".mobile-inner-menu .dropdown-content a.active").html());
    $(".mobile-inner-menu .dropdown-content").hide();
});
$(function() {

  var toolbox = $('.select-dropdown'),
      height = toolbox.height(),
      scrollHeight = toolbox.get(0).scrollHeight;

  toolbox.bind('mousewheel', function(e, d) {
    if((this.scrollTop === (scrollHeight - height) && d < 0) || (this.scrollTop === 0 && d > 0)) {
      e.preventDefault();
    }
  });

});