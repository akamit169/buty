@extends('beautician.main-layout.main-layout')
@section('title') {{'Services'}}@stop
@section('content')      

 <div class="error-msg-div"></div>

     <!-- start content area -->
        <section class="inner-container content-area top-padding services-wrapper">
            <div class="row">
                <div class="col s3 m3 mobile-up">
                    <div class="profile-info-wrap padding0  left-pane">
                        <div class="collapsible-menu">
                            <div class="add-service">
                                <a href="javascript:;" class="waves-effect border-btn">
                                    + Add Your Service
                                </a>
                            </div>
                            <ul class="collapsible" data-collapsible="accordion">
                              @foreach($beauticianServices as $beauticianService)
                                <li>
                                    <div class="collapsible-header">{{strtoupper($beauticianService['name'])}} ({{$beauticianService['childCount']}})</div>
                                    <div class="collapsible-body">
                                       @foreach($beauticianService['children'] as $children)
                                        <div class="added-service" 
                                            data-id="{{$children['id']}}" 
                                            data-service-id="{{$children['service_id']}}" 
                                            data-parent-id="{{$children['parent_service_id']}}" 
                                            data-duration="{{$children['duration']}}"
                                            data-sessions="{{$children['no_of_sessions']}}"
                                            data-cost="{{$children['cost']}}"
                                            data-discount="{{$children['discount']}}"
                                            data-discount_startdate="{{$children['discount_startdate']}}"
                                            data-discounted_days="{{$children['discounted_days']}}"
                                            data-time_btw_sessions="{{$children['time_btw_sessions']}}"
                                            data-description="{{$children['description']}}"
                                            data-tip="{{$children['tip']}}"

                                            >
                                            <div>
                                                <div class="a-d-left">

                                                    <p class="service-name">
                                                        <span class="child-service-name">{{ucwords($children['service_name'])}}</span>

                                                        <span class="tooltipped" data-position="top" data-delay="50" data-tooltip=""><i class="icon icon-info"></i></span>
                                                    </p>
                                                    <div class="service-session"><span class="child-service-session">{{$children['no_of_sessions'] == 0?1:$children['no_of_sessions']}}</span> Session - <span class="child-service-duration">{{$children['duration']}}</span>mins each
                                                        @if(\Auth::user()->cruelty_free_makeup)<br>Cruelty Free @endif</div>
                                                </div>


                                                <div class="a-d-right">
                                                  @if($children['discount'])
                                                  <?php $discountedPrice = $children['cost'] - ($children['cost']*$children['discount'])/100;?>
                                                    <p><span class=" new-price">$<span class="child-service-cost">{{round($discountedPrice,2)}}</span></span><span class="old-price">${{$children['cost']}}</span>
                                                    </p>
                                                  @else
                                                    <p>$<span class="child-service-cost">{{$children['cost']}}</span></p>
                                                  @endif
                                                    <div class="service-session">+ Travel cost</div>
                                                    <a href="#" class="del-services">delete</a>
                                                </div>
                                                <div class="triangle-topright discount-section" style="{{$children['discount']?'display:block;':'display:none;'}}">
                                                    <span class="dis-wrapper"><span class="child-service-discount">{{$children['discount']}}</span>%<br>off</span>
                                                </div>
                                            </div>
                                        </div>
                                       @endforeach
                                    </div>
                                </li>
                              @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col s9 m9 mobile-down">
                    <div class="main-content-area gradient-border services-panel right-pane">
                        <div class="inner-menu-wrapper service-wrap">
                            <div class="inner-heading">Service</div>
                            <div class="tab-section">
                                <div class="common-tabs">
                                    <ul class="tabs main-services">
                                        @foreach($services as $service)
                                            <li class="tab">
                                                <a data-id="{{$service->id}}" class="" href="#" style="">{{ucwords($service->name)}}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="mobile-inner-menu">
                                <!-- Dropdown Trigger -->
                                <a class='dropdown-button' href='#' data-activates='dropdown1'>Select Service</a>
                                <!-- Dropdown Structure -->
                                <ul id='dropdown1' class='dropdown-content main-services'>
                                     @foreach($services as $service)
                                            <li class="tab">
                                                <a data-id="{{$service->id}}" class="" href="#" style="">{{ucwords($service->name)}}</a>
                                            </li>
                                     @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="service-form">
                          <form id="save-service" action="{{url('beautician/createService')}}" method="post" onsubmit="return false" />

                           {!! csrf_field() !!}
                          <input type="hidden" name="parentServiceId" value="">
                          <input type="hidden" name="discountStartDate" value="">
                          <input type="hidden" name="id" value="">
                            <div class="service-checklist">
                                
                            </div>
                            <div class="form-field-wrapper">
                                <div class="row">
                                    <div class="col s6 m6">
                                        <div class="row">
                                            <div class="col s12 m12">
                                                <div class="lage-label">
                                                    Duration
                                                </div>
                                                <div class="form-fields">
                                                    <div class="drop-down">
                                                        <div class="input-field">
                                                            <select name="duration">
                                                                @for($i=15; $i<=180; $i+=15)
                                                                    @if($i == 15)
                                                                        <option value="{{$i}}" selected>{{$i}} mins</option>
                                                                    @else
                                                                        <option value="{{$i}}">{{$i}} mins</option>
                                                                    @endif
                                                                @endfor
                                                                <option value="240">4h</option>
                                                                <option value="300">5h</option>
                                                                <option value="360">6h</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="lage-label">
                                            &nbsp;
                                        </div>
                                        <div class="form-fields">
                                            <div class="check-box-right">
                                                <span class="label-name">Is this a multipack?</span>
                                                <span class="checkboxGroup">
                                                    <input id="rc1" class="checks select-session" type="checkbox">
                                                    <label for="rc1" class=""></label>
                                                </span>
                                            </div>
                                            <div class="d-d-wrapper-left l-disable">
                                                <span class="label-name">Number of Sessions</span>
                                                <div class="drop-down">
                                                    <div class="input-field">
                                                        <select name="sessionNumber">
                                                            <option value="1" selected>1</option>
                                                            <?php for($i=2;$i<=10;$i++) { ?>
                                                              <option value="{{$i}}">{{$i}}</option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-d-wrapper-left l-disable">
                                                <span class="label-name dbs dur-width">Duration Between Sessions</span>
                                                <div class="drop-down">
                                                    <div class="input-field">
                                                        <select name="timeBtwSession">
                                                            <option value="1" selected>1 days</option>
                                                           <?php for($i=2;$i<=7;$i++) { ?>
                                                              <option value="{{$i}}">{{$i}} days </option>
                                                            <?php } ?>

                                                             <option value="14">2 weeks</option>
                                                             <option value="21">3 weeks</option>
                                                             <option value="28">4 weeks</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col s6 m6">
                                        <div class="row">
                                            <div class="col s12 m12">
                                                <div class="lage-label">
                                                    Price (AUD)
                                                    <br><span style="font-size:10px;">per session</span>
                                                </div>
                                                <div class="form-fields">
                                                    <div class="drop-down">
                                                        <div class="drop-down">
                                                            <div class="input-field">
                                                                <div class="input-text">
                                                                    <span class="cost-dollar">$</span>
                                                                    <input type="text" value="" name="cost" class="price-input">
                                                                    <span class="info-text">Not including Travel Costs</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="lage-label">
                                            &nbsp;
                                        </div>
                                        <div class="form-fields">
                                            <div class="check-box-right">
                                                <span class="label-name">Discount (Optional)</span>
                                                <span class="checkboxGroup">
                                                    <input id="dis1" class="checks select-discount" type="checkbox" >
                                                    <label for="dis1" class=""></label>
                                                </span>
                                            </div>
                                            <div class="d-d-wrapper-left r-disable">
                                                <span class="label-name">Start Date</span>
                                                <div class="drop-down width80">
                                                    <div class="input-field">
                                                        <div class="select-wrapper">
                                                          <span class="caret"></span>
                                                          <input value="" type="text" id="datepicker" name="discountStartDateLocal">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-d-wrapper-left input-wrapper r-disable">
                                                <span class="label-name">Discount %</span>
                                                <div class="drop-down width80">
                                                    <div class="input-field">
                                                        <input placeholder="" type="text" name="discount" value="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-d-wrapper-left input-wrapper r-disable">
                                               <span class="label-name">Duration</span>
                                                <div class="drop-down width80">
                                                    <div class="input-field">
                                                        <input placeholder="days" type="text" name="discountedDays" value="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s12 m12">
                                        <div class="dis-text-area">
                                            <p>
                                                Service Description
                                                <span class="tooltipped" data-position="top" data-delay="50" data-tooltip=""><i class="icon icon-info"></i></span>
                                            </p>
                                            <textarea placeholder="" name="description" maxlength="2000"></textarea>
                                            <div class="charcount-wrapper">
                                                <span class="char-count">0</span>/<span class="allowed-char">2000</span>
                                            </div>
                                        </div>
                                        <div class="dis-text-area">
                                            <p>
                                                Preparation tips / After care
                                                <span class="tooltipped" data-position="top" data-delay="50" data-tooltip=""><i class="icon icon-info"></i></span>
                                            </p>
                                            <textarea placeholder="" name="tip" maxlength="2000"></textarea>
                                            <div class="charcount-wrapper">
                                                <span class="char-count">0</span>/<span class="allowed-char">2000</span>
                                            </div>
                                        </div>
                                        <div class="form-button">
                                            <button class="waves-effect border-btn" id="cancel">Cancel</button>
                                            <button class="bg-btn waves-effect" id="save">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          <form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /end container -->
<div id="alert-modal" class="modal">
        <div class="modal-content">
            <div class="row">
                <div class="col s12">
                    <div class="input-field">
                        Are you sure you want to delete the service?
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect border-btn">Cancel</a>
            <button type="submit" class="modal-action bg-btn waves-effect" id="delete-ok" >Ok</button>
            <!--<a href="#!" class="modal-action modal-close bg-btn waves-effect">Save</a>-->
        </div>
    </div>

@stop
@section('scriptjs')
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/service.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/jquery-ui.min.js')}}"></script>
@stop