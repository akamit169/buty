@extends('beautician.main-layout.main-layout')
@section('title') {{'Profile'}}@stop
@section('content')
   
        <!-- start content area -->
        <section class="inner-container top-padding content-area profile-wrapper">
            <div class="row">
                <div class="col s3 m3">
                    <div class="profile-info-wrap">
                        <div class="profile-detail">
                            <div class="profile-options">
                                <ul>
                                    <li class="call">
                                        <a class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="{{$response['beauticianDetails']->phone_number}}">
                                            <i class="icon icon-call"></i>
                                        </a>
                                    </li>
                                    <li class="dis-pic">
                                        @if(!empty($response['beauticianDetails']->profile_pic))
                                            <img src="{{$response['beauticianDetails']->profile_pic}}">
                                        @else    
                                            <img src="{{URL::asset('assets/beautician/images/profile_default.jpg')}}">
                                        @endif
                                    </li>
                                    <li class="camera">
                                        <a href="https://www.instagram.com/{{$response['beauticianDetails']->instagram_link}}" target="_blank">
                                            <i class="icon icon-camera"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <h3>{{ucfirst($response['beauticianDetails']->business_name)}}</h3>
                            <div class="rating">
                                @for($i=1; $i<=$response['beauticianDetails']->rating; $i++)
                                    @if($i == 1)
                                        <span class="stars rated-stars">
                                    @endif    
                                        <i class="icon icon-star"></i>
                                    @if($i == $response['beauticianDetails']->rating)
                                        </span>
                                    @endif
                                    @endfor
                                    @if($response['beauticianDetails']->rating<5)
                                    <span class="stars">
                                        @for($i=$response['beauticianDetails']->rating+1; $i<=5; $i++)
                                            <i class="icon icon-star"></i>
                                        @endfor
                                    </span>    
                                    @endif
                                <span class="rating-num">
                                    ({{$response['beauticianDetails']->review_count}} Reviews)
                                </span>
                            </div>
                            <div class="address">
                                @if($response['beauticianDetails']['mobile_services'])
                                 <i class="icon icon-car"></i>
                                @endif

                                @if($response['beauticianDetails']['address'])
                                 <i class="icon icon-home"></i>
                                 @endif

                                 <?php $addressArr = [];
                                    if($response['beauticianDetails']->address)
                                        array_push($addressArr, $response['beauticianDetails']->address);
                                    if($response['beauticianDetails']->suburb)
                                        array_push($addressArr, $response['beauticianDetails']->suburb);
                                    if($response['beauticianDetails']->state)
                                        array_push($addressArr, $response['beauticianDetails']->state);
                                    if($response['beauticianDetails']->zipcode)
                                        array_push($addressArr, $response['beauticianDetails']->zipcode);

                                    $addressStr = implode(", ",$addressArr);
                                 ?>


                                {{$addressStr}}
                                <br>
                                <div class="abn-number">
                                    ABN {{$response['beauticianDetails']->abn}}
                                </div>
                            </div>
                        </div>
                        <div class="profile-dis">
                            <div class="about-me">
                                <div class="edit-about-me">
                                    <a href="#edit-popup" href="javascript:;">click here to edit your business description summary</a>
                                </div>
                                <div id="business_description">
                                  <?php echo nl2br($response['beauticianDetails']->business_description); ?>
                                </div>
                            </div>
                            <div class="upload-btn">
                                <a class="btn-up-work upload-work-link" href="getPortfolioUpload">
                                    Upload Work
                                    <i class="icon icon-upload"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col s9 m9">
                    <div class="main-content-area">
                        <div class="inner-menu-wrapper">
                            <div class="tab-section">
                                <div class="common-tabs">
                                    <ul class="tabs">
                                        @foreach($services as $service)
                                         <li class="tab main-service" data-service-id="{{$service->id}}"><a class="" href="#service{{$service->id}}" style="">{{$service->name}}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="inner-caption">

                            @foreach($services as $service)
                                <div id="service{{$service->id}}" class="tab-data">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div id="edit-popup" class="modal comon-modal">
            <div class="modal-content text-center">
                <p class="modal-head">Tell us about yourself</p>
                <div class="text-area">
                    <textarea placeholder="Type here..." maxlength="1500"></textarea>
                    <div class="charcount-wrapper">
                        <span class="char-count">0</span>/<span class="allowed-char">1500</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect border-btn">Cancel</a>
                <a href="#!" id="save-description" class="modal-action bg-btn waves-effect">Save</a>
            </div>
        </div>



@stop
@section('scriptjs')
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/lightgallery.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/profile.js')}}"></script>
@stop
    
