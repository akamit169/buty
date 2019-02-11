@extends('beautician.main-layout.main-layout')
@section('title') {{'Bookings'}}@stop
@section('content') 

<?php $bookingDetails = $bookingDetails['bookingDetails'];?>


        <!-- start content area -->
        <section class="inner-container bs-pg" data-booking-id="{{$bookingDetails['id']}}">
            @if(Session::has('photoId'))
            <div class="alert alert-success server-error success-msg-div">
                <label class="text-success">Your post has been sent for an approval to the page admin. Post will get reflected on the page only if the admin approves it.</label><br/>
            </div>
            @endif
            <div class="row">
                <div class="col s12">
                    <div class="b-heading">Booking Summary</div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m4">
                    <div class="bs-left">
                        <div class="pf-pic">
                            <div>
                            <img id="pfImg" src="{{$bookingDetails['profile_pic']}}" />
                            </div>
                        </div>
                        <div class="bp-info">
                            <h3>{{trim($bookingDetails['first_name'].' '.$bookingDetails['last_name'])}}</h3>
                            <div class="rating">
                                <span class="stars">
                                    <?php for ($i=0;$i<5;$i++) { ?>
                                        <i class="icon icon-star{{$i<=($bookingDetails['rating']-1)?' rated':''}}"></i>
                                    <?php } ?>
                                </span>
                                <span class="rating-num">
                                   @if($bookingDetails['review_count'] > 0)
                                     <a href="{{URL('/beautician/getCustomerRating')}}?bookingId={{$bookingDetails['id']}}&userId={{$bookingDetails['customer_id']}}">({{$bookingDetails['review_count']}} Reviews)</a>
                                   @else
                                     <span>({{$bookingDetails['review_count']}} Reviews)</span>
                                   @endif
                                </span>
                            </div>
                            <div class="travel-info">
                            @if($bookingDetails['on_site_service'] == 1)
                             <i class="icon icon-car"></i> 
                            @else
                             <i class="icon icon-home"></i> 
                            @endif

                            {{$bookingDetails['distance']}}km</div>
                        </div>
                        <div class="btn-group">
                            <a class="btn-view-pf" href="{{URL('/beautician/getCustomerProfile')}}?id={{$bookingDetails['customer_id']}}&bid={{$bookingDetails['id']}}">View Profile</a>
                        </div>
                    </div>
                    <div class="customer-details">
                        <div class="row">
                            <div class="col s6">
                                <label>Booking Details</label>
                            </div>
                            <div class="col s6">
                                <label>Booking address : <br>{{$bookingDetails['booking_address']}}</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s6">
                                <label>Payment</label>
                            </div>
                            <div class="col s6">
                             @if($bookingDetails['status'] == \App\Models\CustomerBooking::IS_PAYMENT_DONE || $bookingDetails['status'] == \App\Models\CustomerBooking::DISPUTE_REJECTED_BY_ADMIN)
                              <span class="green-label-text">Paid</span>
                             @elseif($bookingDetails['status'] == \App\Models\CustomerBooking::DISPUTE_RESOLVED_BY_ADMIN)
                                <span class="red-text">Disputed</span>
                            @elseif($bookingDetails['status'] == \App\Models\CustomerBooking::IS_CANCELLED)
                                <span class="red-text">Cancelled</span>
                             @else
                               <span class="red-text">Pending</span>
                             @endif

                             <br>

                              @if(!empty($cardDetail))
                                <label class="customer-del">{{$cardDetail->name}}</label>
                                <label class="customer-del">XXXXXXXXXXXX{{$cardDetail->last4}}</label>
                                <label class="customer-del">{{$cardDetail->exp_month<10?"0".$cardDetail->exp_month:$cardDetail->exp_month}} / {{substr($cardDetail->exp_year, -2)}}</label>
                             @endif
                            </div>
                        </div>
                    </div>
                </div>

                <?php $sessionNo = $bookingDetails['session_no']==0?1:$bookingDetails['session_no']; ?>
                <div class="col s12 m8">
                    <div class="bs-right">
                        <div class="bs-details">
                            <div class="row margin-bottom-none">
                                <div class="col s10 m6 text-left">
                                    <label>{{ucfirst($bookingDetails['service_name'])}}<i class="icon icon-info"></i></label><span>{{$sessionNo}} Session - {{$bookingDetails['duration']}}mins</span></div>
                                <div class="col s2 m6 text-right">
                                   @if($bookingDetails['discount'])
                                    <label class="pre-price">${{$bookingDetails['service_cost']}}</label>
                                  @else
                                    <label class="">${{$bookingDetails['service_cost']}}</label>
                                  @endif
                                </div>
                            </div>
                            @if($bookingDetails['discount'])
                            <div class="row margin-bottom-5">
                                <div class="col s6 m6 text-left">
                                    <label class="discount-value">{{$bookingDetails['discount']}}% Discount</label>
                                </div>
                                <div class="col s6 m6 text-right">
                                    <label class="new-price">${{$bookingDetails['actual_cost']}}</label>
                                </div>
                            </div>
                            @endif

                         @if($bookingDetails['on_site_service'])
                            <div class="row">
                                <div class="col s8 text-left">
                                    <label class="travel-value">Travel Cost (${{$bookingDetails['default_travel_cost']}} x km) </label>
                                </div>
                                <div class="col s4 text-right">
                                    <label class="tr-price">${{$bookingDetails['travel_cost']}}</label>
                                </div>
                            </div>
                         @endif

                            <div class="row margin-bottom-none">
                                <div class="col s2 m6 text-left">&nbsp;</div>
                                <div class="col s10 m6 text-right">
                                    <label class="net-price">Total for this booking <span>${{$bookingDetails['actual_cost'] + $bookingDetails['travel_cost']}}</span></label>
                                </div>
                            </div>
                        </div>
                        <div class="row margin-bottom-none">
                            <div class="col s6 m4 text-left">
                                <label>Session {{$sessionNo}}</label>
                            </div>
                            <div class="col s6 m4 text-right">
                               @if(in_array($bookingDetails['status'],[\App\Models\CustomerBooking::IS_DONE_PAYMENT_LEFT
,\App\Models\CustomerBooking::PAYMENT_HELD]) && $bookingDetails['can_raise_dispute'] != 1)
    <label class="green-label-text b-status">Active</label>
@elseif($bookingDetails['status'] == \App\Models\CustomerBooking::IS_CANCELLED)
   <label class="red-text b-status">Cancelled</label>
@elseif($bookingDetails['status'] == \App\Models\CustomerBooking::IS_DISPUTED_PAYMENT_HELD)
   <label class="red-text b-status">Disputed</label>
@elseif($bookingDetails['status'] == \App\Models\CustomerBooking::DISPUTE_RESOLVED_BY_ADMIN)
       <label class="green-label-text b-status">Dispute Resolved</label>
@elseif($bookingDetails['status'] == \App\Models\CustomerBooking::DISPUTE_REJECTED_BY_ADMIN)
       <label class="green-label-text b-status">Dispute Rejected</label>
@elseif($bookingDetails['status'] == \App\Models\CustomerBooking::IS_PAYMENT_DONE || $bookingDetails['can_raise_dispute'] == 1 || \App\Models\CustomerBooking::DISPUTE_REJECTED_BY_ADMIN)
  <label class="green-label-text b-status">Completed</label>


@else
                                <label class="green-label-text b-status">Pending</label>
@endif
                            </div>
                        </div>
                        <div class="row margin-bottom-5">
                            <div class="col s3 m4 text-left"><span class="">{{$bookingDetails['duration']}}mins</span></div>
                            <div class="col s9 m4 text-right padding-left-none"><span id="booking-start-date"> </span></div>
                        </div>
                        <div class="sep-row"></div>
                        <div class="row margin-bottom-none">
                            <div class="col s12 m8 text-left">
                                <label>Photos</label>
                                 <div class="row margin-top20">
                                    <div class="col s12 padding-left-none">
                                       <ul class="b-pics" id="lightgallery">
                                        <li data-responsive="{{$bookingDetails['natural_image']}}" data-src="{{$bookingDetails['natural_image']}}">
                                        <div class="up-book-pic">
                                           @if($bookingDetails['natural_image'])
                                            <img src="{{$bookingDetails['natural_image']}}">
                                          @endif
                                        </div>
                                        </li>
                                    <li data-responsive="{{$bookingDetails['aspiration_image']}}" data-src="{{$bookingDetails['aspiration_image']}}">
                                        <div class="up-book-pic">
                                            @if($bookingDetails['aspiration_image'])
                                             <img src="{{$bookingDetails['aspiration_image']}}">
                                            @endif
                                        </div>
                                    </li>
                                        </ul>
                                </div>
                            </div>
                            </div>
                            <div class="col s12 m4 text-left">
                                @if($bookingDetails['booking_note'])
                                    <label>Notes</label>
                                    <div class="row margin-top20 notes-div">
                                        <div class="col s12 padding-left-none">
                                            <label class="notes">{{$bookingDetails['booking_note']}}</label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-button booking-sm-btn">
                             @if(($bookingDetails['status'] == \App\Models\CustomerBooking::PAYMENT_HELD || $bookingDetails['status'] == \App\Models\CustomerBooking::IS_DONE_PAYMENT_LEFT) && $bookingDetails['can_raise_dispute'] == 0)
                                <button class="waves-effect border-btn" id="cancel-booking">Cancel Booking</button>
                             @endif
                             @if(($bookingDetails['status'] == \App\Models\CustomerBooking::IS_PAYMENT_DONE || $bookingDetails['status'] == \App\Models\CustomerBooking::IS_DISPUTED_PAYMENT_DONE) && !Session::has('fileName'))
                                <div class="ph-upload-btn">
                                    <form id="image-share" action="{{url('beautician/share-image')}}" method="POST" enctype="multipart/form-data">
                                        <a class="btn-up-work" href="javascript:;">
                                            Upload Photo
                                            <input id="profile-img" type="file" class="file" name="file" accept="image/*" />
                                        </a>
                                        <input type="hidden" name="bookingId" id="bookingId" value="{{$bookingDetails['id']}}" />
                                    </form>
                                </div>
                             @endif
                        </div>
                        @if(Session::has('fileName'))
                        <div class="row margin-top20 notes-div">
                            <div class="col s12 padding-left-none">
                                <div class="share-pic">
                                    <img src="{{URL::asset('temp_images/'.\Auth::user()->id.'/'.Session::get('fileName'))}}" />
                                    <a class="share-btn" href="#share-modal"><i class="fa fa-share-alt share-icon" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        

    <!-- /end container -->
    <div id="share-modal" class="modal">
        <div class="modal-content"> 
            <h4>Share</h4>
            <div class="sep-row"></div>
            <div class="row">
                <div class="col s12 fb-wall-link">
                @if(Session::has('fileName'))    
                    <a href="{{url('beautician/facebook/share-image?file='.Session::get('fileName').'&share=page&bookingId='.$bookingDetails['id'])}}">Share to Beauty Junkie Facebook Wall</a>
                @else
                    <a href="#">Share to Beauty Junkie Facebook Wall</a>
                @endif
                <div class="optional-div">OR</div>
                </div>
            </div>
            <div class="sep-row"></div>
            <div class="row">
                <div class="col s12 text-cen">
                <label>Share with your friends!</label>
                <div class="social-media-icons">
                    <ul>
                        <li>
                            @if(Session::has('fileName'))    
                                <a href="{{url('beautician/facebook/share-image?file='.Session::get('fileName').'&share=me&bookingId='.$bookingDetails['id'])}}" class="fb-icon">Facebook</a>
                            @else
                                <a href="#" class="fb-icon">Facebook</a>
                            @endif    
                        </li>
                    </ul>
                </div>
                </div>
            </div>
            <div class="form-button booking-sm-btn">
                <button class="modal-action modal-close waves-effect border-btn">Cancel</button>
            </div>
        </div>
    </div>

@stop
@section('scriptjs')
 <script type="text/javascript">
     var bookingDate = convertToLocalDateTime("{{$bookingDetails['start_datetime']}}","ddd, D MMMM YYYY / hh:mm a");
     $('#booking-start-date').text(bookingDate);
 </script>
 <script type="text/javascript" src="{{URL::asset('assets/beautician/js/booking-summary.js')}}"></script>

 @stop