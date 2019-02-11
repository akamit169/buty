var bookingDatesHash = {};
var bookingDateSlots = [];
var showPastBookings=0;
var bookingStatus  = {
                      "IS_DONE_PAYMENT_LEFT":1,"PAYMENT_HELD":2,"IS_PAYMENT_DONE":3,
                      "IS_CANCELLED":4,"IS_DISPUTED_PAYMENT_HELD":5,"IS_DISPUTED_PAYMENT_DONE":6,
                      "PAYMENT_FAILED":7,"DISPUTE_RESOLVED_BY_ADMIN":8,"DISPUTE_REJECTED_BY_ADMIN":9
                     };

$(document).ready(function () {

    function onDateSelect(date) 
    {
            window.location.hash=date+"$"+showPastBookings;

            var dateArr = date.split("/");
            var ymd = dateArr[2]+"-"+dateArr[0]+"-"+dateArr[1];

            if(typeof bookingDateSlots[ymd] == "undefined")
            {
                return false;
            }
            
            var selectedDateData = bookingDateSlots[ymd];

            if(showPastBookings == 0)
            {
                listCurrentBookings(selectedDateData);
            }
            else
            {
                listPastBookings(selectedDateData);
            }
          
    }



    function listCurrentBookings(selectedDateData)
    {
            $('.booking-status').remove();
            
            var bookingSlotsHtml = "";
            var scrollPosition=Infinity;
            $.each(selectedDateData,function(key,val){
                var startTimeSplitArr = convertToLocalDateTime(val.startDatetime,"H:m").split(":");
                var startTimeMinutesOffset = (startTimeSplitArr[0]*60)+parseInt(startTimeSplitArr[1]);

                var endTimeSplitArr = convertToLocalDateTime(val.endDatetime,"H:m").split(":");
                var endTimeMinutesOffset = (endTimeSplitArr[0]*60)+parseInt(endTimeSplitArr[1]);
                var positionFromTop = (startTimeMinutesOffset/15)*60;
                var slotHeight = ((endTimeMinutesOffset - startTimeMinutesOffset)/15)*60;

                if(positionFromTop < scrollPosition)
                {
                  scrollPosition = positionFromTop;
                }


                var startDatetime = convertToLocalDateTime(val.startDatetime,"YYYY-MM-DD HH:mm:ss");
                var endDatetime = convertToLocalDateTime(val.endDatetime,"YYYY-MM-DD HH:mm:ss");


                bookingSlotsHtml += '<div data-startdatetime="'+startDatetime+'" data-booking-id="'+val.id+'" class="booking-status booking-brief" style="top:'+positionFromTop+'px;height:'+slotHeight+'px;">'+
                                      '<div class="slot-hide"></div>'+
                                      '<div class="detailed-booking">'+
                                          '<div class="customer-pic"><img src="'+val.profilePic+'"></div>'+
                                          '<div class="customer-data booking-clickable-area">'+capitalizeFirstLetter(val.serviceName)+' for '+(val.firstName+" "+val.lastName).trim()+'</div>';

                                          
                                           var timediff = moment().diff(endDatetime,"minutes");    

                                          if(val.status == bookingStatus.IS_CANCELLED)
                                          {
                                            bookingSlotsHtml +='<div class="booking-action status" style="color:#ff4500">Cancelled</div>';
                                          }
                                          else if(val.status == bookingStatus.IS_DISPUTED_PAYMENT_HELD)  
                                          {
                                            bookingSlotsHtml +='<div class="booking-action status" style="color:#ff4500">Disputed</div>';
                                          }
                                          else if(timediff >= 0)  //in case the service has completed
                                          {
                                            bookingSlotsHtml +='<div class="booking-action status">Completed</div>';
                                          }
                                          else
                                          {
                                            bookingSlotsHtml +='<div class="booking-action cancel">Cancel</div>';
                                          }
                                          

                                       bookingSlotsHtml+='</div>'+
                                    '</div>';


            });

            $(".book-time-list").append(bookingSlotsHtml);

            $('.book-time-list').slimScroll({ scrollTo: scrollPosition });
    }

    function listPastBookings(selectedDateData)
    {

        $('.past-bookings-row').remove();
        var pastBookingsRow = "";


          $.each(selectedDateData,function(key,val){
                var startDate = convertToLocalDateTime(val.startDatetime,"MMMM D, YYYY");
                var startTime = convertToLocalDateTime(val.startDatetime,"hh.mm A");
                var endTime = convertToLocalDateTime(val.endDatetime,"hh.mm A");



                var bookingStatusText = "";
                var color = "";
                var borderColor = "";
                if((val.status == bookingStatus.PAYMENT_HELD || val.status == bookingStatus.IS_DONE_PAYMENT_LEFT) && val.canRaiseDispute != 1)
                {
                  bookingStatusText = "Active";
                  color="#75E3BE";
                  borderColor="linear-gradient(to bottom, rgba( 117,227,190,1) 0%, rgba(105,221,211,1) 100%)";
                }
                else if(val.status == bookingStatus.IS_CANCELLED)
                {
                  bookingStatusText = "Cancelled";
                  color="rgba(192,157,8,1)";
                  borderColor="linear-gradient(to bottom, rgba(241,243,153,1) 0%, rgba(192,157,8,1) 100%)";
                }
                else if(val.status == bookingStatus.IS_DISPUTED_PAYMENT_HELD)
                {
                  bookingStatusText = "Disputed";
                  color="rgba(192,157,8,1)";
                  borderColor="linear-gradient(to bottom, rgba(241,243,153,1) 0%, rgba(192,157,8,1) 100%)";
                }
                else if(val.status == bookingStatus.DISPUTE_RESOLVED_BY_ADMIN)
                {
                  bookingStatusText = "Dispute Resolved";
                  color="#75E3BE";
                  borderColor="linear-gradient(to bottom, rgba( 117,227,190,1) 0%, rgba(105,221,211,1) 100%)";
                }
                else if(val.status == bookingStatus.DISPUTE_REJECTED_BY_ADMIN)
                {
                  bookingStatusText = "Dispute Rejected";
                  color="#75E3BE";
                  borderColor="linear-gradient(to bottom, rgba( 117,227,190,1) 0%, rgba(105,221,211,1) 100%)";
                }
                else if((val.status == bookingStatus.IS_PAYMENT_DONE) || (val.canRaiseDispute == 1) || (bookingStatus.DISPUTE_REJECTED_BY_ADMIN))
                {
                  bookingStatusText = "Completed";
                  color="#75E3BE";
                  borderColor="linear-gradient(to bottom, rgba( 117,227,190,1) 0%, rgba(105,221,211,1) 100%)";
                }   
                else if(val.status == bookingStatus.PAYMENT_FAILED)
                {
                  bookingStatusText = "Payment Failed";
                  color="rgba(192,157,8,1)";
                  borderColor="linear-gradient(to bottom, rgba(241,243,153,1) 0%, rgba(192,157,8,1) 100%)";
                }
                


               pastBookingsRow += '<div class="completed-status past-bookings-row booking-brief" data-booking-id="'+val.id+'" style="border-image-source:'+borderColor+'">'+
                              '<div class="detailed-booking">'+
                                  '<div class="customer-pic"><img src="'+val.profilePic+'"></div>'+
                                  '<div class="status-details booking-clickable-area"><p>'+startDate+'</p>'+
                                  '<p>'+startTime+' - '+endTime+'</p>'+
                                      '<p>'+capitalizeFirstLetter(val.serviceName)+' for '+(val.firstName+" "+val.lastName).trim()+'</p></div>'+
                                  '<div class="status-comp-action" style="color:'+color+'">'+bookingStatusText+'</div>';
                
                var canRaiseDispute = 0;
                var canRaiseDisputeHref = "";
                if(val.canRaiseDispute ==1)
                {
                  canRaiseDisputeHref = SITE_URL+'/beautician/raiseDispute?id='+val.id;
                  pastBookingsRow+='<a class="status-rate-action" href="'+SITE_URL+'/beautician/raiseDispute?id='+val.id+'">Raise a Dispute</a>';
                  canRaiseDispute = 1;
                }
                
                var canRateReview=0;
                var canRateReviewHref="";
                if(val.bookingRatingId != null && val.status == bookingStatus.IS_PAYMENT_DONE)
                {
                  canRateReviewHref =  SITE_URL+'/beautician/rateReviewUser?bookingId='+val.id+'&userId='+val.customerId;
                  pastBookingsRow+='<a class="status-rate-action" href="'+SITE_URL+'/beautician/rateReviewUser?bookingId='+val.id+'&userId='+val.customerId+'">Rate and Review</a>';
                  canRateReview=1;
                }
                
                if(canRateReview== 1 || canRaiseDispute==1)
                {
                   pastBookingsRow+='<a class="status-rate-action-mob" data-canRaiseDisputeHref="'+canRaiseDisputeHref+'" data-canRateReviewHref="'+canRateReviewHref+'" data-showDisputeBtn="'+canRaiseDispute+'" data-showRateBtn="'+canRateReview+'">...</a>';
                }

                              pastBookingsRow+='</div>'+
                          '</div>';

            });

          $('.book-past-list').html(pastBookingsRow);
                        
        
    }


    function initializeDatePicker(latestBookingDate)
    { 
      if(showPastBookings == 0)
      {
        $('#datepicker').multiDatesPicker("destroy");
        $('#datepicker').multiDatesPicker({
        numberOfMonths: [12, 1],
        dayNamesMin: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'],
        minDate:latestBookingDate,
        beforeShowDay: function(d) {
         var isBooked = !(typeof bookingDatesHash[d.toDateString()] == "undefined");
         return [true, isBooked ? "booking-marker d"+moment(d).format('MMDDYYYY') : ""];
         },
         onSelect: function (date, el) {
              onDateSelect(date);
          }
        });

        $('.dates-div').slimScroll({ scrollTo: '0' });
      }
      else
      {
        $('#datepicker').multiDatesPicker("destroy");
        $('#datepicker').multiDatesPicker({
        numberOfMonths: [12, 1],
        dayNamesMin: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'],
        maxDate:latestBookingDate,
        beforeShowDay: function(d) {
         var isBooked = !(typeof bookingDatesHash[d.toDateString()] == "undefined");
         return [true, isBooked ? "booking-marker d"+moment(d).format('MMDDYYYY') : ""];
         },
         onSelect: function (date, el) {
              onDateSelect(date);
          }
        });

        $('.dates-div').slimScroll({ scrollTo: $('.dates-div')[0].scrollHeight});
      }

        

    }


    $(document).on('click','.detailed-booking .cancel',function(){
        var $this = $(this);
        var msg = "Are you sure you want to cancel this booking ?";
      

        var cancel = confirm(msg);
        if(cancel)
        {
           var bookingId = $(this).closest(".booking-status").data("booking-id");

            $('.loader').show();

            $.ajax({
                type: 'POST',
                dataType:"json",
                url: SITE_URL+"/beautician/cancelBooking",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data:{"bookingId":bookingId},
                success: function (response) {
                    $('.loader').hide();
                    alert(response.message);
                    if(response.success)
                    {
                        $this.addClass('status').text("Cancelled");
                    }
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    checkLoginStatus(response.statusCode);
                    
                }
            });
        }
    });

    $(".view-past").click(function () {
        //Custom scroll in past bookings section
        var navHeg = $(".nav-wrapper").height();
        var totHeg = $(window).height();
        var calHeight = totHeg - navHeg - 80;
        var bookHeg = $(".book-past-list").height();
        if (bookHeg > totHeg) {
            $('.book-past-list').slimScroll({
                alwaysVisible: true,
                railVisible: true,
                height: calHeight,
            });
        }
    });
    //Custom scroll in booking list on window resize
        $(window).resize(function () {
            var navHeg = $(".nav-wrapper").height();
            var totHeg = $(window).height();
            var calHeight = totHeg - navHeg - 80;
            var bookHeg = $(".book-past-list").height();
            if (bookHeg > totHeg) {
                $('.book-past-list').slimScroll({
                    alwaysVisible: true,
                    railVisible: true,
                    height: calHeight,
                });
            }
        });

         if ($(window).width() < 768) {
             $(".book-past-list").slimScroll({
                 destroy: true,
                 height: 'auto',
             });
         }
         $(window).resize(function () {
             if ($(window).width() < 768) {
                 $(".book-past-list").slimScroll({
                     destroy: true,
                     height: 'auto',
                 });
             }
         });
    //custom scroll for booking list
    var navHeg = $(".nav-wrapper").height();
    var calHeight = $(window).height() - navHeg - 80;
    $('.book-time-list').slimScroll({
        alwaysVisible: true,
        railVisible: true,
        height: calHeight,
    });
    //Custom scroll in booking list on window resize
    $(window).resize(function () {
        var navHeg = $(".nav-wrapper").height();
        var calHeight = $(window).height() - navHeg - 80;
        $('.book-time-list').slimScroll({
            alwaysVisible: true,
            railVisible: true,
            height: calHeight,
        });
    });

    if ($(window).width() < 768) {
        $(".book-time-list").slimScroll({
            destroy: true,
            height: 'auto',
        });
    }
    $(window).resize(function () {
        if ($(window).width() < 768) {
            $(".book-time-list").slimScroll({
                destroy: true,
                height: 'auto',
            });
        }
    });

    //Custom scroll in calendar
    $(document).ready(function () {
        var topHead = $(".nav-wrapper").height();
        var totHeight = $(window).height() - topHead - 50;
        $('.dates-div').slimScroll({
            alwaysVisible: true,
            railVisible: true,
            height: totHeight,
        });
        //Custom scroll in calendar on window resize
        $(window).resize(function () {
            var topHead = $(".nav-wrapper").height();
            var totHeight = $(window).height() - topHead - 50;
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
        }
        $(window).resize(function () {
            if ($(window).width() < 768) {
                $(".dates-div").slimScroll({
                    destroy: true,
                    height: 'auto',
                });
            }
        });

    });

    var bookings = {};
  
    function getBookings(isPast)
    {
        //reset variables 
        bookingDatesHash = {};
        bookingDateSlots = [];

         var selectedDate;
         //var startDayTimeUTC = moment.tz(moment.tz.guess()).startOf('day').utc().format("HH:mm:ss");
         if(typeof isPast!= "undefined")
         {
            showPastBookings = 1;
            var startDateTime = moment.utc().format("YYYY-MM-DD HH:mm:ss");
         }
         else
         {
           showPastBookings=0;
           var startDateTime = moment.utc().format("YYYY-MM-DD HH:mm:ss");
         }


         var hash = window.location.hash.substr(1);

         var lastDate = "";
         if(hash)
         {
          var lastDateArr = hash.split("$");
          lastDate = lastDateArr[0];
          showPastBookings = lastDateArr[1];
         }

         var ajax_url = SITE_URL+"/beautician/getBeauticianCurrentBooking?startDateTime="+startDateTime+"&showPastBookings="+showPastBookings;
         
         $('.loader').show(); 

         $.ajax({
                type: 'GET',
                dataType:"json",
                url: ajax_url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                cache:false,
                success: function (response) {
                 bookings = response.bookingDetails;
                 $.each(bookings,function(key,val){
                    //set bookingdates hash
                    var localStartDatetime  = convertToLocalDateTime(val.startDatetime,"YYYY-MM-DD");
                    bookingDatesHash[new Date(localStartDatetime).toDateString()] = 1;

                    //create date wise hash with the slot array
                    if(typeof bookingDateSlots[localStartDatetime] == "undefined")
                    {
                        bookingDateSlots[localStartDatetime] = [];
                        bookingDateSlots[localStartDatetime].push(val);
                    }
                    else
                    {
                      bookingDateSlots[localStartDatetime].push(val);
                    }


                 });

                var latestBookingDate = new Date(convertToLocalDateTime(moment.utc().format("YYYY,MM,DD")));
                if(showPastBookings == 1 && bookings.length > 0)
                {   //last past booking date
                    selectedDate = convertToLocalDateTime(bookings[0].startDatetime,"YYYY-MM-DD");
                    latestBookingDate = new Date(convertToLocalDateTime(bookings[0].startDatetime,"YYYY,MM,DD"));
                }
                else if(showPastBookings == 0 && bookings.length > 0)
                {
                  latestBookingDate = new Date(convertToLocalDateTime(bookings[bookings.length-1].startDatetime,"YYYY,MM,DD"));
                  selectedDate = convertToLocalDateTime(bookings[bookings.length-1].startDatetime,"YYYY-MM-DD");
                }

                initializeDatePicker(latestBookingDate);

                 if(lastDate != "")
                 {
                   var elemDate = '.d'+lastDate.split("/").join("");
                   $(elemDate).click();

                   if(showPastBookings == 1)
                   {
                     $('.current-book-div').hide();
                     $('.past-book-div').show();
                   }
                   else
                   {
                     $('.past-book-div').hide();
                     $('.current-book-div').show();
                   }
                 }
                 else
                 {

                     //check if booking is available on the currently selected date and show the slots
                     if(typeof bookingDateSlots[selectedDate] != "undefined")
                     {
                         var selectedDateSplitArr = selectedDate.split("-");
                         var slashSeparatedDate = selectedDateSplitArr[1]+"/"+selectedDateSplitArr[2]+"/"+selectedDateSplitArr[0]
                         onDateSelect(slashSeparatedDate);
                     }
                 }
                
                  $('.loader').hide();
                },
                error: function (response) {
                    var response = JSON.parse(response.responseText);
                    checkLoginStatus(response.statusCode);
                    $('.loader').hide();
                    
                }
            });
    }

    getBookings();

    //toggle bookings div

    $(".view-past").click(function () {
        $(".past-book-div").show();
        $(".current-book-div").hide();

       window.location.hash = "";
       $('.book-past-list').html("");

        getBookings(true);

    });

    $(".view-current").click(function () {
        $(".current-book-div").show();
        $(".past-book-div").hide();

         window.location.hash = "";
         $('.book-past-list').html("");

        getBookings();
});

$(document).on("click",".booking-clickable-area",function(){
  var elem = $(this).closest(".booking-brief");
  location.href=SITE_URL+"/beautician/booking-details?id="+elem.data("booking-id");
});

$(document).on('click','.status-rate-action-mob',function(){

    $('#rate-button-modal .dispute-modal').show();
    $('#rate-button-modal .rate-modal').show();

    var $this = $(this);
    var showDisputeBtn = $this.data('showdisputebtn');
    var showDisputeBtnHref = $this.data('canraisedisputehref');
    var showRateBtn = $this.data('showratebtn');
    var showRateBtnHref = $this.data('canratehref');


    $('#rate-button-modal .dispute-modal a')[0].href = showDisputeBtnHref;
    $('#rate-button-modal .rate-modal a')[0].href = showRateBtnHref;

    if(showDisputeBtn != 1)
    {
      $('#rate-button-modal .dispute-modal').hide();
    }

    if(showRateBtn != 1)
    {
      $('#rate-button-modal .rate-modal').hide();
    }

   $('#rate-button-modal').modal('open');
});


  
});
