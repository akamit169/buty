@extends('beautician.main-layout.main-layout')
@section('title') {{'Bookings'}}@stop
@section('content') 

<?php 
  $customer = $response['user']; 
  $customerDetails = $customer->customer_details;
  $bookingId = \Request::input('bid');


?>

        <!-- start content area -->
        <section class="inner-container">
           <div class="customer-profile">
            <div class="row">
                <div class="col s12 m5">
                    <div class="profile-info-wrap">
                        <div class="profile-detail">
                            <div class="profile-options">
                                <ul>    
                                    <li class="call">
                                        <a href="javascript:;">
                                            <i class="icon icon-call"></i>
                                        </a>
                                    </li>
                                    <li class="dis-pic">
                                        <img src="{{$customer->profile_pic}}">
                                    </li>
                                     <li class="flag-icon">
                                        <a href="{{url('beautician/flagCustomer?id='.$customer->id)}}">
                                            <i></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <h3>{{trim($customer->first_name." ".$customer->last_name)}}</h3>
                            <div class="rating customer-rate">
                                <span class="stars">

                                    <?php for ($i=0;$i<5;$i++) { ?>
                                        <i class="icon icon-star{{$i<=($customer->rating-1)?' rated':''}}"></i>
                                    <?php } ?>

                                </span>
                                <span class="rating-num">
                                   @if($customer->review_count > 0)
                                     <a href="{{URL('/beautician/getCustomerRating')}}?bookingId={{$bookingId}}&userId={{$customer->id}}">({{$customer->review_count}} Reviews)</a>
                                   @else
                                     <span>({{$customer->review_count}} Reviews)</span>
                                   @endif

                                </span>
                            </div>
                            <div class="address">
                                <?php $addressArr = []; ?>
                                     @if($customer->address)
                                       <?php array_push($addressArr, $customer->address); ?>
                                     @endif

                                    @if($customer->suburb)
                                       <?php array_push($addressArr, $customer->suburb); ?>
                                     @endif

                                     @if($customer->state)
                                       <?php array_push($addressArr, $customer->state); ?>
                                     @endif

                                     @if($customer->zipcode)
                                       <?php array_push($addressArr, $customer->zipcode); ?>
                                     @endif

                                     {{implode(", ",$addressArr)}}
                                <br>
                                <div class="abn-number">
                                    {{$customer->gender==\App\Models\User::GENDER['MALE']?'Male':($customer->gender==\App\Models\User::GENDER['FEMALE']?'Female':'Other')}} &#8226; 
                                    <?php 
                                        $dateObj = new \DateTime($customer->date_of_birth); 
                                    ?>
                                    {{$dateObj->format("j M Y")}}
                                </div>
                            </div>
                        </div>
                        <div class="profile-dis">
                            <div class="about-me">
                                {{$customerDetails->description}}
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="cst-details">
                <div class="col s12 m7">
                 <div class="row">
                     <div class="col s3"><label>SKIN</label></div>
                     <div class="col s4"><div class="skin-color"><img src="{{$customerDetails->skinColorImage}}"></div><label>Type &#8226; {{$customerDetails->skinType}}</label></div>
                 </div>
                 <div class="sep-row"></div>
                 <div class="row">
                     <div class="col s3"><label>HAIR</label></div>
                     <div class="col s4"><label>Coloured &#8226; {{$customerDetails->is_hair_colored == 1?'Yes':'No'}}</label> <label>Type &#8226; {{$customerDetails->hairType}}</label> <label>Length &#8226; {{$customerDetails->hairlengthType}}</label></div>
                 </div>
                 <div class="sep-row"></div>
                 <div class="row">
                     <div class="col s3"><label>ALLERGIES, MEDICATION <br>OR HEALTH CONDITIONS?</label></div>
                     <div class="col s4"><label>{{$customerDetails->allergies?'Yes':'No'}}</label> <label>{{$customerDetails->allergies}}</label></div>
                 </div>
                </div>
               </div>
            </div>
            </div>
        </section>


@stop