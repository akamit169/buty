  function convertToLocalDateTime(utcDatetime,datetimeformat)
  {
     if(typeof datetimeformat == "undefined")
     {
      datetimeformat = 'YYYY-MM-DD HH:mm:ss';
     }
     var supported_format_updated_at = utcDatetime.replace(/-/g, "/");
     var updated_at_UTC = new Date(supported_format_updated_at+" UTC");
     var updated_at_localdatetime = updated_at_UTC.toString();
     var datetime = moment(updated_at_localdatetime).format(datetimeformat);
     return datetime;
  }


  function capitalizeFirstLetter(string) {
    var tokens = string.split(" ");

    var stringArr = [];
    for(i=0;i<tokens.length;i++)
    {
      stringArr.push(tokens[i].charAt(0).toUpperCase() + tokens[i].slice(1));
    }

    return stringArr.join(" ");
}


$(document).ready(function () {
    $('.profile-sub-nav').hide();
    $('.setting-sub-nav').hide();
    if (window.location.href.indexOf("beautician/setting") > -1) {
        $('.setting-sub-nav').show();
    } else {
        $('.profile-sub-nav').show();
    }
    $('select').material_select();

    //mobile tab view
    $(document).load($(window).bind("resize", listenWidth));

    function listenWidth(e) {
        if ($(window).width() < 768) {
            $(".home-tab").addClass("mobile-tabs");
            $('.home-tab ul.tabs').hide();
            $(".home-tab .active-tab").click(function (e) {
                e.stopPropagation();
                $('.home-tab ul.tabs').slideToggle();
            });
            $('.home-tab .active-tab').click(function (e) {
                e.stopPropagation();
            });
            $('html').click(function () {
                $('.mobile-tab ul.tabs').slideUp();
            });
            $('.mobile-tabs .tabs .tab a').click(function () {
                $('.mobile-tabs ul.tabs').slideUp();
            });
            $('html').on('click', '.home-tab .tab a', function () {
                $(".home-tab .active-tab").html($(".home-tab .tab a.active").html());
            });

            //Mobile Navigation
            $('html').on("click",".menu-icon", function () {
                $(".menu-icon").removeClass("menu-icon fa-bars").addClass("icon-close");
                $(".nav-wrapper").slideDown();
                $(".main-header").css("height", "100%");
                $(".inner-container").css("display", "none");
                $(".profile-nav>a").attr("href", "javascript:;");
                $(".setting-nav>a").attr("href", "javascript:;");
            });
            
            $('html').on("click", ".icon-close", function () {
                $(".icon-close").removeClass("icon-close").addClass("menu-icon fa-bars");
                $(".nav-wrapper").slideUp();
                $(".main-header").css("height", "59px");
                $(".inner-container").css("display", "block");
            });
            $('.profile-nav').click(function () {
                $(".profile-sub-nav").slideDown();
                $(".nav-wrapper ul li.profile-nav").css("border-bottom","2px solid #474747");
                $(".nav-wrapper ul li.profile-nav a").css("border-bottom","none");
                $(".inner-container").addClass('top-padding');
                $(".setting-sub-nav").slideUp();
            });
            $('.profile-sub-nav a').click(function () {
                $(".profile-sub-nav").slideUp();
                $(".profile-nav .arrow").toggleClass('fa-angle-up , fa-angle-down ');
            });
            $('.setting-nav').click(function () {
                $(".setting-sub-nav").slideDown();
                $(".nav-wrapper ul li.setting-nav").css("border-bottom","2px solid #474747");
                $(".nav-wrapper ul li.setting-nav a").css("border-bottom","none");
                $(".inner-container").addClass('top-padding');
                $(".profile-sub-nav").slideUp();
            });
            $('.setting-sub-nav a').click(function () {
                $(".setting-sub-nav").slideUp();
                $(".setting-nav .arrow").toggleClass('fa-angle-up , fa-angle-down ');
            });
            
            $('.profile-sub-nav').hide();

        } else {
            $(".home-tab .active-tab").hide();
            $(".home-tab").removeClass("mobile-tabs");
            $('.home-tab ul.tabs').show();
            //$(".profile-nav>a").attr("href", "profile.htm");
            //$(".setting-nav a").attr("href", "setting.htm");
            $(".main-header").css("height", "59px");
            $(".nav-wrapper").show();

           
            var substring = "beautician/profile";
            if(location.href.indexOf(substring) == -1)
            {
              $('.profile-sub-nav').hide();
            }
            else
            {
              $('.profile-sub-nav').show();
            }
            
        }
    };
    listenWidth();

    //Mobile Navigation
    /* $(document).ready(function () {
         $(".nav-wrapper li").click(function () {
             $(".nav-wrapper li").removeClass('active');
             $(this).addClass('active');

         });
     });*/


    $(document).on('click', '.upload-work-link', function () {
        $(".profile-sub-nav").hide();
    });

    var headerAlign = $('header').children().length;
    if(headerAlign == 2){
        $(".sign-btn").addClass("right-align");
    }else{
        $(".sign-btn").removeClass("right-align");
    }


//on notifications mouseover


/**
 * 
 * @param {type} data
 * @param {type} appendRow
 * @returns {undefined}
 */
    function appendNotificationsInDOM(data)
    {
        var html = "";

        var i=0;
        $.each(data,function(key,value){
            
              if(i == 5)
                return false;

              html += createNotificationsRowHtml(value,key);   
              i++;
        });


        var arrLen = data.length;
        if(arrLen)
        {
          var ntfhtml = '<ul class="notification-items">'+html+'</ul>';

          if(arrLen > 5)
          {
               ntfhtml+='<div class="view-all-noti" style="text-align:center;">'+
                         '<a href="'+SITE_URL+"/beautician/notifications"+'">View all</a>'+
                        '</div>';

          }

          $('.notification-sub-menu').html(ntfhtml);      
        }
        else
        {
            $('.notification-sub-menu').html("<div style='text-align:center;padding: 20px 0;'>No Notifications Available</div>");
        }

    }

      $(document).mouseup(function () {
         $('.notification-sub-menu').fadeOut();
      });

      $('.notification-sub-menu').on('click',function(){
        location.href=SITE_URL+"/beautician/notifications";
      })


/**
 *  
 * @param {type} val
 * @param {type} key
 * @returns {String}
 */
    function createNotificationsRowHtml(val,key)
    {  
        var profilePic = val.profilePic;
        if(profilePic == "")
        {
          profilePic = SITE_URL+'/assets/beautician/images/bj-admin.png';
        }

        var readClass="";
        if(val.isRead == 1)
        {
          readClass = ' read';
        }

       
        var ntfHtml =  '<li class="selected2 notification-row'+readClass+'" data-profilePic="'+profilePic+'" data-type="'+val.type+'" = data-serviceName="'+val.serviceName+'" data-customerName="'+val.customerName+'" data-startdatetime="'+val.startDatetime+'" data-enddatetime="'+val.endDatetime+'" data-booking-id="'+val.bookingId+'" data-id="'+val.id+'">'+
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

        return ntfHtml;
    }



    function getNotificationsAjax()
    {

        var ajax_url = SITE_URL + '/beautician/notificationsListAjax';
     
        $.ajax({
                type: 'GET',
                dataType:"json",
                url: ajax_url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {

                if(response.success == true)
                {
                  notifications = response.list.data;
                  appendNotificationsInDOM(notifications);  
                  $('.notification-sub-menu').show();         
                }
                else
                {
                  location.href=response.redirectUrl;
                }
                    
                },
                error: function (response) {
                    var response = JSON.parse(response.responseText);
                    checkLoginStatus(response.statusCode);
                    
                }
            });
    }


    function updateNewNotificationsCount()
    {
       $.ajax({
                type: 'GET',
                dataType:"json",
                url: SITE_URL+"/beautician/notification/new",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {
                   if(response.success == true)
                   {
                    if(response.newCount == 0)
                    {
                      $('#notifications-menu-tab .badge').addClass('hide').text("");
                    }
                    else
                    {
                      $('#notifications-menu-tab .badge').removeClass('hide').text(response.newCount);
                    }
                   }         

                },
                error: function (response) {
                    var response = JSON.parse(response.responseText);
                    checkLoginStatus(response.statusCode);
                    
                }
            });
    }


    if(typeof SITE_URL !== "undefined" && IS_USER_LOGGEDIN)
    {
      setInterval(function(){
        updateNewNotificationsCount();
      },30*1000);

    }
    

    //on notifications menu click
  $('#notifications-menu-tab').on('click',function(e){
      e.preventDefault();
      if($(window).width() < 768)
      {
        location.href=SITE_URL+"/beautician/notifications";
      }
      else if(!$('.all-notification').length)
      {
         getNotificationsAjax();
      }
      else
      {
        location.reload();
      }

      $('#notifications-menu-tab .badge').addClass('hide');
     
  });

});





//tabs change
$(document).ready(function () {
    $('ul.tabs').tabs();
});


//modal view
$(document).ready(function () {
    $('.modal').modal({
        backdrop: 'static',
        endingTop: '25%'
    });
});


function showValidationError(msg) {
    var msg = $('<textarea />').html(msg).text();
    $('.error-msg-div').text(msg).slideDown();
    setTimeout(function () {
            $('.error-msg-div').slideUp();
        },
        5000);
}


$( "body" ).mousemove(function( event ) {
    setTimeout(function(){
        $('.server-error').fadeOut();
    }, '2000');
   
});
