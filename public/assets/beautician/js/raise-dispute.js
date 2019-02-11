$(function(){
  $('#submit').on('click',function(){

        var $this = $(this);
        var cancel = confirm("Are you sure you want to raise a dispute for this booking ?");
        if(cancel)
        {
           var bookingId = $('.inner-container').data("booking-id");
           var customerId = $('.inner-container').data("customer-id");
           var beauticianId = $('.inner-container').data("beautician-id");
           var reason = $('textarea').val();

            $('.loader').show();

            $.ajax({
                type: 'POST',
                dataType:"json",
                url: SITE_URL+"/beautician/raiseDispute",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data:{"bookingId":bookingId,"customerId":customerId,"beauticianId":beauticianId,"reason":reason},
                success: function (response) {
                    $('.loader').hide();
                    showValidationError(response.message);
                    if(response.success == true)
                    {
                       $this.prop("disabled",true);
                       $('.b-status').text("Disputed");
                       setTimeout(function(){
                            history.back();
                        },2000)
                    }              
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    showValidationError(response.message);
                    checkLoginStatus(response.statusCode);
                    $('.ajax-loader').hide();
                    
                }
            });
        }



  });


  $('textarea').on('keyup',function(){
       var charLength = $(this).val().length;

       $(this).parent().find('.char-count').text(charLength);

});


})