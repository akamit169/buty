@extends('beautician.main-layout.main-layout') @section('title') {{'Client Rating'}}@stop @section('content')

<!-- start container -->
<div class="inner-container">
    @if (isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissable server-error success-msg-div">    
                <h4><i class="icon fa fa-ban"></i> Error !</h4>
                @foreach($errors->all() as $key=>$message)
                <label class="error-msg">* {{$message}}</label><br/>
                @endforeach
            </div>
    @elseif (Session::has('status'))
                <div class="alert alert-success server-error success-msg-div">
                    <h4><i class="icon fa fa-ban"></i> Success !</h4>
                    <label class="text-success">{{Session::get('status')}}</label><br/>
                </div>
    @endif
    @if(Session::get('error_msg')) 
                <div class="alert alert-danger alert-dismissable server-error success-msg-div">  
                    <h4><i class="icon fa fa-ban"></i> Error !</h4>
                    {{Session::get('error_msg')}}
                </div>
    @elseif(Session::get('success_msg'))
                <div class="alert alert-success server-error success-msg-div">
                    <h4><i class="icon fa fa-check"></i> Success !</h4>
                    {{Session::get('success_msg')}}
                </div>
    @endif 
    <div class="booking-wrapper padding-bottom-50">
        <div class="b-heading">Rate & Review  <!--{{ucwords($bookingDetail->customerFirstName)}} for {{ucwords($bookingDetail->serviceName)}} --> </div>
        <div class="rate-user-name"><div class="profile-pic"><img src="{{$bookingDetail->profile_pic}}"></div><div class="n-content"><div class="n-name-time">{{ucwords($bookingDetail->customerFirstName)}}</div><div class="n-dis"> {{ucwords($bookingDetail->serviceName)}}</div></div></div>
        <form method="POST" action="{{url('beautician/rateReviewUser')}}" onsubmit="return validateForm();">
            <input type="hidden" name="bookingId" value="{{$bookingId}}" />
            <input type="hidden" name="userId" value="{{$userId}}" />
            <input type="hidden" name="rating" value="" />
            {!! csrf_field() !!}
            
        <div class="">
            <div class="b-rate-review">
                <div class="b-rating">
                    <label>
                        <input type="radio" name="ratingPoint" value="5" title="5 stars"> 5
                    </label>
                    <label>
                        <input type="radio" name="ratingPoint" value="4" title="4 stars"> 4
                    </label>
                    <label>
                        <input type="radio" name="ratingPoint" value="3" title="3 stars"> 3
                    </label>
                    <label>
                        <input type="radio" name="ratingPoint" value="2" title="2 stars"> 2
                    </label>
                    <label>
                        <input type="radio" name="ratingPoint" value="1" title="1 star"> 1
                    </label>
                </div>
            </div>
            <div class="booking-form">
                <textarea placeholder="Your review here..." name="comment" maxlength="250"></textarea>
                <div class="charcount-wrapper">
                    <span class="char-count">0</span>/<span class="allowed-char">250</span>
                </div>
                <div class="row sel-reason">
                    <div class="col s12 m12 hide reasonDiv">
                        <div class="lage-label">
                            Select Reason
                        </div>
                        <div class="form-fields">
                            <div class="drop-down">
                                <div class="input-field">
                                    <select name="reasonId">
                                        @foreach($reasonMaster as $key=>$value)
                                            @if($key == 0)
                                                <option value="{{$value['id']}}" selected>{{$value['reason']}}</option>
                                            @else
                                                <option value="{{$value['id']}}">{{$value['reason']}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="b-button">
                    <button class="bg-btn waves-effect">Submit Review</button>
                </div>
            </div>
        </div>
        </form>    
    </div>
</div>
<!-- /end container -->
@stop @section('scriptjs')
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/spinner.js')}}"></script>
<script type="text/javascript">
    $(function(){
        $('.b-rating input').change(function () {
            var $radio = $(this);
            $('.b-rating .selected').removeClass('selected');
            $radio.closest('label').addClass('selected');
            if($('.selected').length > 0) {
                var i = 1;
                $('.selected').nextAll('label').each(function(index, value){
                    i++;
                });
                $('input[name="rating"]').val(i);
                if(i <= 3) {
                    $('.reasonDiv').removeClass('hide');
                } else {
                    $('.reasonDiv').addClass('hide');
                }
            } else {
                $('input[name="rating"]').val('');
            }
        });

       $('textarea').on('keyup',function(){
           var charLength = $(this).val().length;
           $(this).parent().find('.char-count').text(charLength);

        });
    });
    function validateForm() {
        var msg = '';
        if($('input[name="rating"]').val() == '') {
            msg += '- Please provide rating.\n';
        }

        if(msg != '') {
            alert(msg);
            return false;
        }
        return true;
    }
</script>
@stop