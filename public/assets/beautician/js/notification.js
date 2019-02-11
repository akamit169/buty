/* start js for top logout setion */
  var windowWidth = $( window ).width();
  $navNotification = $('.nav-notification');
  $bodyTag = $('body');
  $notificationMubMenu = $('.notification-sub-menu');
  var isOpen = false;
  $navNotification.click(function () {
      if (isOpen == false && windowWidth > 767) {
          $notificationMubMenu.fadeIn();
          isOpen = true;
      } else {
          $notificationMubMenu.fadeOut();
          isOpen = false;
      }
  });

  $notificationMubMenu.mouseup(function () {
      return false;
  });

  $(document).mouseup(function () {
      if (isOpen == true) {
          $navNotification.click();
      }
  });

  /* js for notification detail */
  $('html').on('click', '#btn-running-late', function(){
    $('#on-time').hide();
    $('#running-late').fadeIn();
  });

  $('html').on('click', '#btn-on-time', function(){
    $('#running-late').hide();
    $('#on-time').fadeIn();
  });


 //Custom scroll in calendar
    var topHead = $(".nav-wrapper").height();
    var totHeight = $(window).height() - topHead - 50;
    $('.notification-items').slimScroll({
        alwaysVisible: true,
        railVisible: true,
        height: totHeight,
    });
    //Custom scroll in calendar on window resize
    $(window).resize(function () {
        var topHead = $(".nav-wrapper").height();
        var totHeight = $(window).height() - topHead - 50;
        $('.notification-items').slimScroll({
            alwaysVisible: true,
            railVisible: true,
            height: totHeight,
        });
    });

if ($(window).width() < 768) {
        $(".notification-items").slimScroll({
            destroy: true,
            height: 'auto',
        });


         $(window).on('scroll',function(){
                var heightx = $(document).height() - $(window).height();
              
                var scrollheight = $(window).scrollTop();
               if(totalNotifications && (heightx - scrollheight <= 1) && totalNotifications != $('.notification-row').length && call_in_progress == 0 ) {
                     call_in_progress = 1;
                     getRequestsAjax(true);
                  }
          });
}
 $(window).resize(function () {
        if ($(window).width() < 768) {
          
            $(".notification-items").slimScroll({
                destroy: true,
                height: 'auto',
            });


             $(window).on('scroll',function(){
                var heightx = $(document).height() - $(window).height();
              
                var scrollheight = $(window).scrollTop();
               if(totalNotifications && (heightx - scrollheight <= 1) && totalNotifications != $('.notification-row').length && call_in_progress == 0 ) {
                     call_in_progress = 1;
                     getRequestsAjax(true);
                  }
          });
        }
 });

   var page = 1;
   var call_in_progress = 0;
   var per_page;
   var new_request = 1;
   var notifications;
   var totalNotifications=0;

   var notificationType = {"ONE_DAY_BEFORE_BOOKING":1,"BOOKING_CANCELLED":3,"BOOKING_DONE":4,"SET_AVAILABILITY":8,"BEAUTICIAN_ONTIME_CONFIRMATION" : 5,"IS_RATING_PENDING":2};

/**
 * 
 * @param {type} data
 * @param {type} appendRow
 * @returns {undefined}
 */
    function appendNtfInDOM(data,appendRow)
    {
        var html = "";
        $.each(data,function(key,value){
              html += createRowHtml(value,key);   
        });



        if(html)
        {
            if(appendRow)
            {
                $('.notification-items').append(html);
            }
            else
            {
                $('.notification-items').html(html);
            }  
                
        }
        else
        {
            $('.notification-items').html("<div style='text-align:center;padding:20px 0;'>No Notifications Available</div>");
        }

    }



    /**
 * 
 * @param {type} val
 * @param {type} key
 * @returns {String}
 */
        function createRowHtml(val,key)
        {    
            var profilePic = val.profilePic;
            if(profilePic == "")
            {
              profilePic = '../assets/beautician/images/bj-admin.png';
            }

            var readClass="";
            if(val.isRead == 1)
            {
              readClass = ' read';
            }
            var html =  '<li class="selected2 notification-row'+readClass+'" data-recipient-id="'+val.recipientId+'" data-profilePic="'+profilePic+'" data-type="'+val.type+'" = data-serviceName="'+val.serviceName+'" data-customerName="'+val.customerName+'" data-startdatetime="'+val.startDatetime+'" data-enddatetime="'+val.endDatetime+'" data-booking-id="'+val.bookingId+'" data-id="'+val.id+'">'+
                            '<a href="javascript:;">'+
                                '<div class="profile-pic"><img src="'+profilePic+'"></div>'+
                                '<div class="n-content">'+
                                    '<div class="n-name-time">'+val.senderFirstName+
                                        '<span class="n-time pull-right">'+
                                            '<i class="fa fa-clock-o" aria-hidden="true"></i> '+$.timeago(convertToLocalDateTime(val.createdAt))+
                                        '</span>'+
                                    '</div>'+
                                    '<div class="n-dis">'+val.message+'</div>'+
                                '</div>'+
                            '</a>'+
                        '</li>';
            return html;
        }
 

    function getRequestsAjax(append)
    {

        if(per_page)
        {
             var start = $('.notification-row').length;
             page = Math.ceil(start/per_page) + 1;

        }

        var ajax_url = SITE_URL + '/beautician/notificationsListAjax?page='+page;
     

        $('.ajax-loader').show();
        $.ajax({
                type: 'GET',
                dataType:"json",
                url: ajax_url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {

                  notifications = response.list.data;
                  per_page = response.list.perPage;
                  totalNotifications = response.list.total;

                  if(notifications.length == 0 && !new_request)
                  {
                    $('.ajax-loader').hide();
                    return;
                  }
                  else
                  {

                    appendNtfInDOM(notifications,append);

                    $('.ajax-loader').hide();
                    call_in_progress = 0;
                  }  
                    
                },
                error: function (response) {
                    var response = JSON.parse(response.responseText);
                    checkLoginStatus(response.statusCode);
                    $('.ajax-loader').hide();
                    
                }
            });
    }


  getRequestsAjax();


  $(document).on('click','.notification-row',function(){

      $('.notification-details').hide();
      var ntfType = $(this).attr('data-type');
      var bookingId = $(this).attr('data-booking-id');
      var recipientId = $(this).attr('data-recipient-id');

      //on time confirmations
      if( ntfType == notificationType.BEAUTICIAN_ONTIME_CONFIRMATION)
      {
        var startDatetime = $(this).attr('data-startdatetime');
        var endDatetime = $(this).attr('data-enddatetime');
        var serviceName = $(this).attr('data-serviceName');
        var customerName = $(this).attr('data-customerName');

          //return in case the service has started
         var timediff = moment().diff(convertToLocalDateTime(startDatetime,"YYYY-MM-DD HH:mm:ss"),"minutes");
        
         if(timediff >= 0)
         {
          return false;
         }
  

        $('.notification-details').attr('data-bookingId',bookingId);
        $('.notification-details').attr('data-customerName',customerName);

        var onTime = '<div class="n-detail" id="on-time">'+
                            '<div class="n-d-content">'+
                             '<div class="profile-pic"><img src="'+$(this).attr('data-profilePic')+'"></div>'+
                                '<div class="n-d-text">'+
                                    '<div>'+convertToLocalDateTime(startDatetime,"MMMM D, YYYY hh.mm A")+' - '+convertToLocalDateTime(endDatetime,"hh.mm A")+' </div>'+
                                    '<div>'+capitalizeFirstLetter(serviceName)+' for '+customerName+'</div>'+
                                '</div>'+
                            '</div>'+
                            '<div class="n-button">'+
                                '<button id="btn-running-late" class="waves-effect border-btn">I’m running late</button>'+
                                '<button class="bg-btn right waves-effect time-confirmation ontime"  >I’m on time </button>'+
                            '</div>'+
                        '</div>';

        var runningLate = '<div class="n-detail" id="running-late">'+
                            '<div class="n-d-content">'+
                                '<p>Select Delay Time</p>'+
                                 '<div class="drop-down">'+
                                    '<div class="input-field">'+
                                        '<select id="select-dropdown">'+
                                            '<option value="15"selected>15 mins</option>'+
                                            '<option value="30">30 mins</option>'+
                                            '<option value="45">45 mins</option>'+
                                            '<option value="60">60 mins</option>'+
                                        '</select>'+
                                    '</div>'+
                                '</div>'+
                                '<p>A notification will be sent to '+customerName+' regarding the delay</p>'+
                            '</div>'+
                            '<div class="n-button">'+
                                '<button id="btn-on-time" class="waves-effect border-btn">Cancel</button>'+
                                '<button class="bg-btn right waves-effect time-confirmation delay"  >Confirm</button>'+
                            '</div>'+
                        '</div>';

        $('.notification-details').html(onTime+runningLate);
        $('.notification-details').show();

        $('#select-dropdown').material_select();
      }

      if(!$(this).hasClass('read'))
      {
         var notificationId = $(this).attr('data-id');
         var ajax_url = SITE_URL + '/beautician/notification/markRead';
         $.ajax({
                type: 'POST',
                dataType:"json",
                url: ajax_url,
                data:{'notificationId':notificationId},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {
                    
                },
                error: function (response) {
                    var response = JSON.parse(response.responseText);
                    checkLoginStatus(response.statusCode);
                    $('.ajax-loader').hide();
                    
                }
            });
        $(this).addClass('read');
      }


      if(ntfType == notificationType.ONE_DAY_BEFORE_BOOKING || ntfType == notificationType.BOOKING_CANCELLED || ntfType == notificationType.BOOKING_DONE)
      {
        location.href=SITE_URL+"/beautician/booking-details?id="+bookingId;
      }
      else if(ntfType == notificationType.IS_RATING_PENDING)
      {
        location.href=SITE_URL+"/beautician/rateReviewUser?bookingId="+bookingId+"&userId="+recipientId;   
      }





  });


  $(document).on('click','.time-confirmation',function(){

    $(this).prop('disabled',true); 

    var ajax_url = SITE_URL + '/beautician/setBeauticianTimeliness';
    var postData = {"bookingId":$('.notification-details').attr('data-bookingId')};
    if($(this).hasClass('delay'))
    {
      postData.delay = $('#select-dropdown').val();
    }

    var customerName = $('.notification-details').attr('data-customerName');

    var msg = "Thanks for confirming, a notification regarding the same has been sent to the "+customerName;
    

    $.ajax({
                type: 'POST',
                dataType:"json",
                url: ajax_url,
                data:postData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {

                      
                      if(response.success == true)
                      {
                        alert(msg);
                        if ($(window).width() < 768)
                        {
                          $('.back-arrow').trigger('click');
                        }
                        else
                        {
                          $('.notification-details').hide();
                        }


                      }
                      else
                      {
                         alert(response.message);
                         $('.ajax-loader').hide();
                      }

                      
                    
                },
                error: function (response) {
                    var response = JSON.parse(response.responseText);
                    alert(response.msg);
                    checkLoginStatus(response.statusCode);
                    $('.ajax-loader').hide();
                    
                }
            });

  });


if(windowWidth > 767){
var $scrollable = $('.notification-items');
$scrollable.slimScroll().
        bind('slimscroll', function(e, pos){
            if(pos == 'bottom') {     

               if(totalNotifications && totalNotifications != $('.notification-row').length && call_in_progress == 0 ) {
                     call_in_progress = 1;
                     getRequestsAjax(true);
                  }

            }
        });

}

  /* mobile service */
  if(windowWidth < 767){
      $('html').on('click', '.nav-notification', function(){
        window.location.href = 'notification-list.htm';
      });

      $('html').on('click', '.notification-items li', function(){
          $('.list-for-mobile').hide();
          $('.detail-for-mobile').show();
          $('.back-arrow').show();
          $('.menu-icon').hide();
      })
      $('html').on('click', '.back-arrow', function(){
          $('.list-for-mobile').show();
          $('.detail-for-mobile').hide();
          $('.back-arrow').hide();
          $('.menu-icon').show();
      })
      } 
    