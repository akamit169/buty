
if(arePaymentDetailsSet == 0)
{
  alert("Payment Details must be set before setting availability.");
  location.href = "beauticianPaymentDetail";
}


$(document).ready(function () {

    getBeauticianAvailableDate();
    var bookedFlag = 0;
    var arrSelectedDate = [];
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    var y = today.getFullYear();
    if ($(window).width() < 768) {
        $('#datepicker').multiDatesPicker({
            numberOfMonths: [1, 1],
            todayHighlight: true,
            minDate: today,
            startDate: today,
            maxPicks: 1,
            dayNamesMin: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'],
            onSelect: function (date, el) {
                setAvailability(date, el);
            }
        });
    }
    $(window).resize(function () {
        if ($(window).width() < 768) {
            $('#datepicker').multiDatesPicker({
                numberOfMonths: [1, 1],
                todayHighlight: true,
                minDate: today,
                startDate: today,
                maxPicks: 1,
                dayNamesMin: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'],
                onSelect: function (date, el) {
                    setAvailability(date, el);
                }
            });
        }
    });

    $('#datepicker').multiDatesPicker({
        numberOfMonths: [12, 1],
        todayHighlight: true,
        minDate: today,
        startDate: today,
        maxPicks: 1,
        dayNamesMin: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'],
        onSelect: function (date, el) {
            setAvailability(date, el);
        }
    });

    //Select Multiple dates
    $(document).on('click', '.multi-sel', function () {
        arrSelectedDate = [];
        $('.data-list').html('');
        $(".multi-sel").removeClass("multi-sel").addClass("single-sel");
        $(".single-sel").html("Select Single");
        $('#datepicker').multiDatesPicker({
            maxPicks: Infinity,
            onSelect: function (date, el) {
                setAvailability(date, el);
            }
        });
        $('#datepicker').multiDatesPicker('resetDates');
        $('#datepicker').datepicker('setDate', 'today');
        $(".da")
        getBeauticianAvailableDate();
    });

    //Select single date
    $(document).on('click', '.single-sel', function () {
        arrSelectedDate = [];
        $('.data-list').html('');
        $(".single-sel").removeClass("single-sel").addClass("multi-sel");
        $(".multi-sel").html("Select Multiple");
        $('#datepicker').multiDatesPicker({
            maxPicks: 1,
            onSelect: function (date, el) {
                setAvailability(date, el);
            }
        });
        $('#datepicker').multiDatesPicker('resetDates');
        $('#datepicker').datepicker('setDate', 'today');;
        getBeauticianAvailableDate();
    });

    //Set current date

    $(document).on('click', '.move-date', function () {
        arrSelectedDate = [];
        $('.data-list').html('');
        if($(".cell-header a").hasClass("multi-sel")){
        $('#datepicker').multiDatesPicker('resetDates');
        }else{
            $('#datepicker').datepicker('setDate', 'today');
        }
        $('.dates-div').slimScroll({
            scrollTo: '0px'
        });
         getBeauticianAvailableDate();
    });
    
    //Custom scroll in calendar
    var topHead = $(".nav-wrapper").height();
    var subHead = $(".setting-sub-nav").height();
    var totHeight = $(window).height() - topHead - subHead - 120;
    $('.dates-div').slimScroll({
        alwaysVisible: true,
        railVisible: true,
        height: totHeight,
    });
    //Custom scroll in calendar on window resize
    $(window).resize(function () {
        var topHead = $(".nav-wrapper").height();
        var subHead = $(".setting-sub-nav").height();
        var totHeight = $(window).height() - topHead - subHead - 120;
        $('.dates-div').slimScroll({
            alwaysVisible: true,
            railVisible: true,
            height: totHeight,
        });
    });
    //Mobile screen calendar view
    if ($(window).width() < 768) {
        $(".dates-div").slimScroll({
            destroy: true,
            height: 'auto',
        });
        $(document).on('click', '.move-date', function () {
            arrSelectedDate = [];
            $('.data-list').html('');
            $('#datepicker').datepicker('setDate', 'today');
            $('.dates-div').slimScroll({
                destroy: true,
                height: 'auto',
            });
            getBeauticianAvailableDate();
        });
        $(document).on('click', '.ui-datepicker-next , .ui-datepicker-prev', function () {
            getBeauticianAvailableDate();
        });
    }
    $(window).resize(function () {
        if ($(window).width() < 768) {
            $(".dates-div").slimScroll({
                destroy: true,
                height: 'auto',
            });
            $(document).on('click', '.move-date', function () {
                arrSelectedDate = [];
                $('.data-list').html('');
                $('#datepicker').datepicker('setDate', 'today');
                $('.dates-div').slimScroll({
                    destroy: true,
                    height: 'auto',
                });
            });
            getBeauticianAvailableDate();
        }
    });

    $(function () {
        $('.defaultEntry').timeEntry({
            timeSteps: [1, 30, 0],
            show24Hours: true,
            initialField: null,
            noSeparatorEntry: true,
            maxTime: new Date(0, 0, 0, 12, 30, 0),
    });
    });

    $('#datepicker').on('click', '.ui-state-highlight', function () {
        getBeauticianAvailableDate();
    });

    function setAvailability(date, el) {
        getBeauticianAvailableDate();
        var day = el.selectedDay,
            mon = el.selectedMonth,
            year = el.selectedYear;
        var el = $(el.dpDiv).find('[data-year="' + year + '"][data-month="' + mon + '"]').filter(function () {
            return $(this).find('a').text().trim() == day;
        });
        if (el.hasClass('ui-state-highlight')) {
            bookedFlag = 0;
            //check if any booking has been done on this date
            var validateBooking = 0;
            $.ajax({
                url: SITE_URL + '/beautician/setting/checkBooking',
                type: "GET",
                data: {
                    date: formatDate(date),
                    timezone: moment.tz.guess(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                async: false,
                success: function (response) {
                    if (response.success == false) {
                        alert(response.message);
                        validateBooking = 1;
                    } else {
                        bookedFlag = response.bookedFlag;
                        $('.data-list').html('');
                        arrSelectedDate.push(date);
                        if (arrSelectedDate.length > 0) {
                            renderAvailabilityData(arrSelectedDate);
                        } else {
                            $('.data-list').html('');
                        }
                    }
                },
                error: function (response) {
                    var response = JSON.parse(response.responseText);
                    alert(response.message);
                }
            });

        } else {
            if (arrSelectedDate.length > 0) {
                var index = $.inArray(date, arrSelectedDate);
                if (index != -1) {
                    arrSelectedDate.splice(index, 1);
                    if(arrSelectedDate.length > 0) {
                        renderAvailabilityData(arrSelectedDate);
                    }
                }
            }
        }
        //        if (arrSelectedDate.length > 0) {
        //            renderAvailabilityData(arrSelectedDate);
        //        } else {
        //            $('.data-list').html('');
        //        }
        if (arrSelectedDate.length == 0) {
            $('.data-list').html('');
        }
    }

    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;

        return [year, month, day].join('-');
    }
    //format time
    function formatTime(timeStr) {
        var colon = timeStr.indexOf(':');
        var hours = timeStr.substr(0, colon),
            minutes = timeStr.substr(colon + 1, 2),
            meridian = timeStr.substr(colon + 4, 2).toUpperCase();

        var hoursInt = parseInt(hours, 10),
            offset = meridian == 'PM' ? 12 : 0;

        if (hoursInt === 12) {
            hoursInt = offset;
        } else {
            hoursInt += offset;
        }
        if (hoursInt < 10) {
            hoursInt = "0" + hoursInt;
        }
        return hoursInt + ":" + minutes + ":00";
    }
    //set time range
    $('html').on('click', '.set-availability-dt', function () {
        var msg = '';
        var slot1From = '',
            slot1To = '',
            slot2From = '',
            slot2To = '',
            slot1FromDate = '',
            slot1ToDate = '',
            slot2FromDate = '',
            slot2ToDate = '',
            slot2ToLast = '',
            slot2FromLast = '';
        var todayDate = getCurrentDate();
        if (!$('.slot1-checkbox').is(':checked')) {
            slot1From = $('input[name="slot1From"]').val(); 
            var slot1FromTimeZone = '';
            if(slot1From != '') {
                slot1FromTimeZone = $('.slot1FromTimeZone :selected').val();
            }
            if(slot1FromTimeZone == '') {
                msg += '- Please select AM/PM for Slot 1 From timing.\n';
            }
            slot1From += slot1FromTimeZone;
            var slot1FromLast = slot1From.slice(-2);
            slot1To = $('input[name="slot1To"]').val();
            var slot1ToTimeZone = '';
            if(slot1To != '') {
                slot1ToTimeZone = $('.slot1ToTimeZone :selected').val();
            }
            if(slot1ToTimeZone == '') {
                msg += '- Please select AM/PM for Slot 1 To timing.\n';
            }
            slot1To += slot1ToTimeZone;
            var slot1ToLast = slot1To.slice(-2);
            if (slot1To == '') {
                msg += '- Please enter Slot 1 To timing.\n';
            }
            if (slot1From == '') {
                msg += '- Please enter Slot 1 from timing.\n';
            }

            if (slot1To != '' || slot1From != '') {
                slot1From = insertString(slot1From, ' ', slot1FromLast);
                slot1To = insertString(slot1To, ' ', slot1ToLast);
                slot1FromDate = todayDate + ' ' + slot1From;
                slot1ToDate = todayDate + ' ' + slot1To;
                if (Date.parse(slot1ToDate) <= Date.parse(slot1FromDate)) {
                    msg += '- Slot1 To timing can not be greater than  or equal to slot1 From timing.\n';
                }
            }

        } else {
            slot1From = '';
            slot1To = '';
            $('input[name="slot1From"]').val('');
            $('input[name="slot1To"]').val('');
        }
        if (!$('.slot2-checkbox').is(':checked')) {
            slot2From = $('input[name="slot2From"]').val();
            var slot2FromTimeZone = '';
            if(slot2From != '') {
                slot2FromTimeZone = $('.slot2FromTimeZone :selected').val();
            }
            if(slot2FromTimeZone == '') {
                msg += '- Please select AM/PM for Slot 2 From timing.\n';
            }
            slot2From += slot2FromTimeZone;
            slot2FromLast = slot2From.slice(-2);
            slot2To = $('input[name="slot2To"]').val();
            var slot2ToTimeZone = '';
            if(slot2To != '') {
                slot2ToTimeZone = $('.slot2ToTimeZone :selected').val();
            }
            if(slot2ToTimeZone == '') {
               msg += '- Please select AM/PM for Slot 2 To timing.\n'; 
            }
            slot2To += slot2ToTimeZone;
            slot2ToLast = slot2To.slice(-2);
            if (slot2To == '') {
                msg += '- Please enter Slot 2 To timing.\n';
            }
            if (slot2From == '') {
                msg += '- Please enter Slot 2 from timing.\n';
            }
            if (slot2To != '' || slot2From != '') {
                slot2From = insertString(slot2From, ' ', slot2FromLast);
                slot2To = insertString(slot2To, ' ', slot2ToLast);
                var slot2FromDate = todayDate + ' ' + slot2From;
                var slot2ToDate = todayDate + ' ' + slot2To;
                if (Date.parse(slot2ToDate) <= Date.parse(slot2FromDate)) {
                    if(slot2From != "12:00 PM" && slot2To != "12:00 AM")
                    {
                      msg += '- Slot2 To timing can not be greater than or equal to slot2 From timing.\n';  
                    }
                }


                if(slot2ToTimeZone == "AM" && slot2To != "12:00 AM")
                {
                  msg += '- Invalid time range in Slot2'; 
                }

            }
        } else {
            slot2From = '';
            slot2To = '';
            $('input[name="slot2From"]').val('');
            $('input[name="slot2To"]').val('');
        }

        // if (slot2FromDate != '' && slot2ToDate != '') {
        //     if (Date.parse(slot2FromDate) >= Date.parse(slot1FromDate) && Date.parse(slot2FromDate) <= Date.parse(slot1ToDate)) {
        //         msg += '- Slot2 From timing can not be between slot1 From and slot1 To timing.\n';
        //     }
        //     if (Date.parse(slot2ToDate) >= Date.parse(slot1FromDate) && Date.parse(slot2ToDate) <= Date.parse(slot1ToDate)) {
        //         msg += '- Slot2 To timing can not be between slot1 From and slot1 To timing.\n';
        //     }
        // }
        if (msg != '') {
            alert(msg);
        } else {
            var arrSelectedDateLength = arrSelectedDate.length;
            var startDatetime = [];
            var endDatetime = [];
            var isAvailable = [];
            var slot = [];
            var id = [];
            var formatedSlot1FromTime = formatTime(slot1From);
            var formatedSlot1ToTime = formatTime(slot1To);
            var formatedSlot2FromTime = formatTime(slot2From);
            var formatedSlot2ToTime = formatTime(slot2To);
            var arrAvailabilityDetails = [];
            if (arrSelectedDateLength > 0) {
                var j = 0;
                for (var i = 0; i < arrSelectedDateLength; i++) {
                    //render(arrSelectedDate[i]);
                    //for 1st slot timing
                    var formatedStartDateTime = '',
                        formatedEndDateTime = '',
                        slot1Id = '',
                        slot2Id = '',
                        formatedIsAvailable = 0;
                    var formatedDate = formatDate(arrSelectedDate[i]);
                    if (!$('.slot1-checkbox').is(':checked')) {
                        formatedStartDateTime = formatedDate + ' ' + formatedSlot1FromTime;
                        formatedStartDateTime = convertToUTCDateTime(formatedStartDateTime);
                        formatedEndDateTime = formatedDate + ' ' + formatedSlot1ToTime;
                        formatedEndDateTime = convertToUTCDateTime(formatedEndDateTime);
                        formatedIsAvailable = 1;
                    } else {
                        formatedStartDateTime = convertToDateTime(formatedDate);
                        formatedStartDateTime = convertToUTCDateTime(formatedStartDateTime);
                        formatedEndDateTime = formatedStartDateTime;
                    }
                    $('input[type="hidden"]').each(function (index, value) {
                        if ($(this).hasClass('slot1Date') && $(this).val() == formatedDate) {
                            slot1Id = $(this).siblings('.id').val();
                        }
                        if ($(this).hasClass('slot2Date') && $(this).val() == formatedDate) {
                            slot2Id = $(this).siblings('.id').val();
                        }
                    });

                    startDatetime.push(formatedStartDateTime);
                    endDatetime.push(formatedEndDateTime);
                    isAvailable.push();
                    slot.push(1);
                    arrAvailabilityDetails.push({
                        id: slot1Id,
                        startDatetime: formatedStartDateTime,
                        endDatetime: formatedEndDateTime,
                        isAvailable: formatedIsAvailable,
                        slot: 1
                    });
                    //end of for 1st slot timing
                    //for 2nd slot timing
                    var formatedStartDateTime2 = '',
                        formatedEndDateTime2 = '',
                        formatedIsAvailable2 = 0;
                    if (!$('.slot2-checkbox').is(':checked')) {
                        formatedStartDateTime2 = formatedDate + ' ' + formatedSlot2FromTime;
                        formatedStartDateTime2 = convertToUTCDateTime(formatedStartDateTime2);
                        formatedEndDateTime2 = formatedDate + ' ' + formatedSlot2ToTime;
                        formatedEndDateTime2 = convertToUTCDateTime(formatedEndDateTime2);
                        formatedIsAvailable2 = 1;
                    } else {
                        formatedStartDateTime2 = convertToDateTime(formatedDate);
                        formatedStartDateTime2 = convertToUTCDateTime(formatedStartDateTime2);
                        formatedEndDateTime2 = formatedStartDateTime2;
                    }
                    startDatetime.push(formatedStartDateTime2);
                    endDatetime.push(formatedEndDateTime2);
                    isAvailable.push(formatedIsAvailable2);
                    slot.push(2);
                    arrAvailabilityDetails.push({
                        id: slot2Id,
                        startDatetime: formatedStartDateTime2,
                        endDatetime: formatedEndDateTime2,
                        isAvailable: formatedIsAvailable2,
                        slot: 2
                    });
                    //end of for 2nd slot timing
                }
                if (startDatetime.length > 0 && endDatetime.length > 0) {
                    $('.loader').show();
                    $.ajax({
                        url: SITE_URL + '/beautician/setting/setAvailability',
                        type: 'POST',
                        data: {
                            arrAvailabilityDetails: arrAvailabilityDetails
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.success == true) {
                                //get availability data
                                renderAvailabilityData(arrSelectedDate, 1);
                                arrSelectedDate = [];
                                $('#datepicker').multiDatesPicker('resetDates');
                                getBeauticianAvailableDate();
                            }
                            $('.loader').hide();
                            alert(response.message);
                        },
                        error: function (response) {
                            $('.loader').hide();
                            var response = JSON.parse(response.responseText);
                            alert(response.message);
                        }
                    });
                }
            }
            $('.loader').show();
            $('.loader').hide();
            $('.modal-close').trigger('click');
        }

    });

    $('html').on('click', '.slot2-checkbox', function () {
        if ($(this).is(':checked')) {
            $('input[name="slot2From"]').val('');
            $('input[name="slot2To"]').val('');
            $('input[name="slot2To"]').attr('disabled', true);
            $('input[name="slot2From"]').attr('disabled', true);
            $('.slot2FromTimeZone li:eq( 0 )').trigger('click');
            $('.slot2ToTimeZone li:eq( 0 )').trigger('click');
        } else {
            $('input[name="slot2To"]').removeAttr('disabled');
            $('input[name="slot2From"]').removeAttr('disabled');
        }
    });
    $('html').on('click', '.slot1-checkbox', function () {
        if ($(this).is(':checked')) {
            $('input[name="slot1From"]').val('');
            $('input[name="slot1To"]').val('');
            $('input[name="slot1To"]').attr('disabled', true);
            $('input[name="slot1From"]').attr('disabled', true);
            $('.slot1FromTimeZone li:eq( 0 )').trigger('click');
            $('.slot1ToTimeZone li:eq( 0 )').trigger('click');
        } else {
            $('input[name="slot1To"]').removeAttr('disabled');
            $('input[name="slot1From"]').removeAttr('disabled');
        }
    });

    function getCurrentDate() {
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();

        if (dd < 10) {
            dd = '0' + dd
        }
        if (mm < 10) {
            mm = '0' + mm
        }
        return today = mm + '/' + dd + '/' + yyyy;
    }

    function insertString(a, b, at) {
        var position = a.indexOf(at);

        if (position !== -1) {
            return a.substr(0, position) + b + a.substr(position);
        }

        return "";
    }

    function convertToDateTime(localDatetime) {
        var UtcYMD = moment.utc(localDatetime, "YYYY-MM-DD").format("YYYY-MM-DD HH:mm:ss");
        return UtcYMD;
    }

    function renderAvailabilityData(arrSelectedDate, returnFromSave) {
        returnFromSave = (returnFromSave !== undefined) ? returnFromSave : 0;
        $.ajax({
            url: SITE_URL + '/beautician/setting/getAvailabilityData',
            data: {
                selectedDate: arrSelectedDate,
                timezone: moment.tz.guess(),
                returnFromSave: returnFromSave
            },
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            type: 'POST',
            success: function (response) {
                $('.data-list').html(response);
            },
            error: function (response) {
                var response = JSON.parse(response.responseText);
                alert(response.selectedDate);
            }
        });
    }

    function convertToUTCDateTime(localDatetime) {
        var UtcYMD = moment(localDatetime, "YYYY-MM-DD HH:mm:ss").utc().format("YYYY-MM-DD HH:mm:ss");
        return UtcYMD;
    }

    $('html').on('click', '.set-date', function () {
        if(bookedFlag == 1) {
            alert('A booking has been made for this date so you can not edit the availability for the same.'); return false;
        }
        $('input[name="slot1From"]').removeAttr('disabled');
        $('input[name="slot1To"]').removeAttr('disabled');
        $('.slot1-checkbox').prop('checked', false);
        $('input[name="slot2From"]').removeAttr('disabled');
        $('input[name="slot2To"]').removeAttr('disabled');
        $('.slot2-checkbox').prop('checked', false);
        if ($('.ui-state-highlight').length == 1) {
            var date = $('.ui-state-active').html();
            var year = $('.ui-state-active').closest('td').attr('data-year');
            var month = $('.ui-state-active').closest('td').attr('data-month');
            if(month < 10) {
                month = "0"+month;
            }
            if(date<10) {
                date = "0"+month;
            }
            if(arrSelectedDate.length == 0) {
                var selectedDate = formatDate(month+"/"+date+"/"+year);
            } else {
                var selectedDate = formatDate(arrSelectedDate[0]);
            }
            selectedDate = moment(selectedDate).format('dddd, D MMMM YYYY');
            $('.popup-date').html(selectedDate + '<i class="icon icon-info info-tip" title="Timing for different slots can be entered in 15 minutes interval only."></i>');
        } else {
            $('.popup-date').html('Multiple dates<i class="icon icon-info info-tip" title="Timing for different slots can be entered in 15 minutes interval only."></i>');
        }
        var slot1FromTime = '', slot1ToTime = '', slot2FromTime = '', slot2ToTime= '';
        if($('.slot1FromTime').length > 0) {
            slot1FromTime = $('.slot1FromTime').html();
            slot1ToTime = $('.slot1ToTime').html();
            if($('.slot2FromTime').length == 0) { 
                slot2FromTime = '';
            }
        } else if($('.slot1Date').eq(0).length > 0) {
            slot1FromTime = '';
        }
        if($('.slot2FromTime').length > 0) {
            slot2FromTime = $('.slot2FromTime').html();
            slot2ToTime = $('.slot2ToTime').html();
            if(slot1FromTime == 'none') {
                slot1FromTime = '';
            }
        } else if($('.slot2Date').eq(0).length > 0) {
            slot2FromTime = '';
        }
        if (slot1FromTime != '' && slot1FromTime !== undefined) {
            slot1FromTime = slot1FromTime.replace(/\s/g, '');
            slot1ToTime = slot1ToTime.replace(/\s/g, '');
        }
        if ((slot1FromTime != 'none' || slot1FromTime === undefined) && slot1FromTime == '')  {
            $('input[name="slot1From"]').attr('disabled', true);
            $('input[name="slot1To"]').attr('disabled', true);
            $('.slot1-checkbox').trigger('click');
        } else if(slot1FromTime == 'none') {
            slot1FromTime = '';
            $('input[name="slot1From"]').removeAttr('disabled');
            $('input[name="slot1To"]').removeAttr('disabled');
            $('.slot1-checkbox').prop('checked', false);
        }
        if (slot2FromTime != '' && slot2FromTime !== undefined) {
            slot2FromTime = slot2FromTime.replace(/\s/g, '');
            slot2ToTime = slot2ToTime.replace(/\s/g, '');
        }
        
        if (slot2FromTime != 'none' || slot2FromTime === undefined) {
            if(slot2FromTime == '') {
                $('input[name="slot2From"]').attr('disabled', true);
                $('input[name="slot2To"]').attr('disabled', true);
                $('.slot2-checkbox').trigger('click');
            }
        } else if(slot2FromTime == 'none') {
            slot2FromTime = '';
            $('input[name="slot2From"]').removeAttr('disabled');
            $('input[name="slot2To"]').removeAttr('disabled');
            $('.slot2-checkbox').prop('checked', false);
        }
        var slot1FromTimeZone = slot1FromTime.slice(-2);
        slot1FromTime = slot1FromTime.slice(0,-2);
        var slot1ToTimeZone = slot1ToTime.slice(-2);
        slot1ToTime = slot1ToTime.slice(0,-2);
        var slot2FromTimeZone = slot2FromTime.slice(-2);
        slot2FromTime = slot2FromTime.slice(0,-2);
        var slot2ToTimeZone = slot2ToTime.slice(-2);
        slot2ToTime = slot2ToTime.slice(0,-2);
        $('input[name="slot1From"]').val(slot1FromTime.replace(/\s/g, ''));
        $('select.slot1FromTimeZone').val(slot1FromTimeZone).material_select();
        $('input[name="slot1To"]').val(slot1ToTime.replace(/\s/g, ''));
        $('select.slot1ToTimeZone').val(slot1ToTimeZone).material_select();
        $('input[name="slot2From"]').val(slot2FromTime.replace(/\s/g, ''));
        $('select.slot2FromTimeZone').val(slot2FromTimeZone).material_select();
        $('input[name="slot2To"]').val(slot2ToTime.replace(/\s/g, ''));
        $('select.slot2ToTimeZone').val(slot2ToTimeZone).material_select();
    });
    
    function getBeauticianAvailableDate() {
        //get beautician already available dates
        $.ajax({
            url: SITE_URL + '/beautician/setting/getBeauticianAvailableDates',
            type: 'GET',
            data: {timezone: moment.tz.guess()},
            success: function(response) {
                if(response.success == true) {
                    $(response.data).each(function(index, value){
                        $('td[data-handler="selectDay"]').each(function(sindex, svalue){
                            var month = parseInt($(this).attr('data-month'))+1;
                            if(month < 10) {
                                month = '0'+month;
                            }
                            var date = $(this).find('a').html();
                            if(date < 10) {
                                date = '0'+date;
                            }
                            var calendarDate = $(this).attr('data-year')+'-'+month+'-'+date;
                            
                            if(calendarDate == value) {
                                $(this).addClass('booked');

                            }
                        });
                    });

                }
            },
            error: function(response) {

            }
        }); 
    }

});