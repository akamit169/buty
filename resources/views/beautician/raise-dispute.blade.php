@extends('beautician.main-layout.main-layout')
@section('title') {{'Dispute'}}@stop
@section('content')  
  
   <div class="error-msg-div"></div>

  <?php $bookingDetails = $bookingDetails['bookingDetails'];?>
         <div class="inner-container" data-booking-id="{{$bookingDetails['id']}}" data-customer-id="{{$bookingDetails['customer_id']}}" data-beautician-id="{{$bookingDetails['beautician_id']}}" >
            <div class="booking-wrapper">
               <div class="b-heading">Raise a Dispute</div>
                 <div class="">
                    <ul class="added-booking">
                       <li>
                          <div class="profile-pic">
                            <img src="{{$bookingDetails['profile_pic']}}">
                          </div>
                          <div class="booking-detail">
                            <div class="row">
                              <div class="col s8">
                                <div class="b-date">February 8, 2018</div>
                                <div class="b-time">08.45AM - 09.00AM</div>
                                <div class="b-text">{{ucfirst($bookingDetails['service_name'])}} for {{$bookingDetails['first_name']}}</div>    
                              </div>
                              <div class="col s4">
                                <div class="b-status">Completed</div>
                              </div>
                            </div>
                          </div>    
                       </li>
                    </ul>
                    <div class="booking-form">
                      <textarea placeholder="Your comment here" maxlength="500"></textarea>
                      <div class="charcount-wrapper">
                        <span class="char-count">0</span>/<span class="allowed-char">500</span>
                     </div>
                      <div class="b-button">
                        <button class="bg-btn waves-effect" id="submit">Submit a Dispute</button>
                      </div>
                    </div>
                 </div>
            </div>
         </div>

@stop

@section('scriptjs')
  <script type="text/javascript">
        var startDate = convertToLocalDateTime("{{$bookingDetails['start_datetime']}}","MMMM D, YYYY");
        var startTime = convertToLocalDateTime("{{$bookingDetails['start_datetime']}}","hh.mm A");
        var endTime = convertToLocalDateTime("{{$bookingDetails['end_datetime']}}","hh.mm A");
       $('.b-date').text(startDate);
       $('.b-time').text(startTime+' - '+endTime);
   </script>
  <script type="text/javascript" src="{{URL::asset('assets/beautician/js/raise-dispute.js')}}"></script>
 @stop