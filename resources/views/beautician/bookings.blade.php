@extends('beautician.main-layout.main-layout')
@section('title') {{'Bookings'}}@stop
@section('content')      

      <!-- start content area -->
        <section class="inner-container">
            <div class="booking-section">
                <div class="calendar-div">
                    <div class="sel-book-div">
                        <div class="dates-div">
                            <div id="datepicker"></div>
                        </div>
                    </div>
                </div>
              
              <div class="booking-list-view">
                  <div class="current-book-div">
                      <div class="booking-header"><div class="heading-view view-past">View Past Bookings</div><div class="sep"></div></div>
                      <div class="book-time-list">
                          <div class="time-list">

                             <div class="slots">12:00 AM</div>
                             <div class="slots">12:15 AM</div>
                             <div class="slots">12:30 AM</div>
                             <div class="slots">12:45 AM</div>

                             <?php 
                                for($i=1;$i<=11;$i++)
                                {
                                  for($j=0;$j<=45;$j+=15)
                                  {
                                    $hour = sprintf("%02d", $i);
                                    $min = sprintf("%02d", $j);
                                    echo '<div class="slots">'.$hour.':'.$min.' AM</div>';
                                  }
                                }
                             ?>

                              
                              
                             <div class="slots">12:00 PM</div>
                             <div class="slots">12:15 PM</div>
                             <div class="slots">12:30 PM</div>
                             <div class="slots">12:45 PM</div>

                             <?php 
                                for($i=1;$i<=11;$i++)
                                {
                                  for($j=0;$j<=45;$j+=15)
                                  {
                                    $hour = sprintf("%02d", $i);
                                    $min = sprintf("%02d", $j);
                                    echo '<div class="slots">'.$hour.':'.$min.' PM</div>';
                                  }
                                }
                             ?>

                          </div>
                      
                      </div>
                  </div>
                    <div class="past-book-div" style="display:none;">
                      <div class="booking-header"><div class="heading-view view-current">View Current Bookings</div><div class="sep"></div></div>
                      <div class="book-past-list">
                        
                           
                      </div>
                  </div>
              </div>
            </div>
        </section>

        

        <div id="rate-button-modal" class="modal">
        <div class="modal-content">
            <div class="row dispute-modal">
                <div class="col s12">
                    <a href="#" class="modal-action modal-close waves-effect border-btn">Raise a Dispute</a>
                </div>
            </div>
            <div class="row rate-modal">
                <div class="col s12">
                    <a href="#" class="modal-action modal-close bg-btn waves-effect">Rate & Review</a>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <a href="#!" class="modal-action modal-close waves-effect border-btn">Cancel</a>
                </div>
            </div>
        </div>
    </div>


@stop
@section('scriptjs')
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/calendar.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/booking.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/jquery.slimscroll.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/jquery.plugin.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/jquery.timeentry.js')}}"></script>
@stop