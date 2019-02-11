@extends('beautician.main-layout.main-layout') @section('title') {{'My Profile'}}@stop @section('content')

<section class="inner-container top-padding">
    <form method="POST" id="beauticianDetailProfile" class="beauticianDetailProfile" action="{{url('beautician/setting/beauticanProfile')}}" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <input type="hidden" id="lat" class="lat" name="lat" />
        <input type="hidden" id="lng" class="lng" name="lng" /> @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissable server-error success-msg-div">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-ban"></i> Error !</h4> @foreach($errors->all() as $key=>$message)
            <label class="error-msg">* {{$message}}</label>
            <br/> @endforeach
        </div>
        @elseif (Session::has('status'))
        <div class="alert alert-success server-error success-msg-div">
            <label class="text-success">{{Session::get('message')}}</label>
            <br/>
        </div>
        @endif
        <div class="edit-profile">
            <div class="pf-pic">
                <div>
                    @if(!empty($beauticianDetail['beauticianDetails']->profile_pic))
                    <img id="pfImg" src="{{$beauticianDetail['beauticianDetails']->profile_pic}}"> @else
                    <img id="pfImg" src="{{URL::asset('assets/beautician/images/profile_default.jpg')}}"> @endif
                </div>
            </div>
            <a href="#" class="edit-pic">
                <input type="file" name="profilePic" id="profile-img" accept="image/*">edit</a>
            <div class="row">
                <div class="col s12">
                    <label class="data-label">{{ucwords($beauticianDetail['beauticianDetails']->first_name).' '.ucwords($beauticianDetail['beauticianDetails']->last_name)}}</label>
                    <label class="data-label">ABN {{$beauticianDetail['beauticianDetails']->abn}}</label>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="input-field">
                        <input id="address" name="address" type="text" class="address validate" value="{{$beauticianDetail['beauticianDetails']->address}}">
                        <label for="address">Home Salon/Private Studio Address (optional)</label>
                    </div>
                </div>
            </div>
           

             <div class="row">
                <div class="col s6">
                    <div class="input-field">
                        <input id="suburb" name="suburb" type="text" class="suburb validate" value="{{$beauticianDetail['beauticianDetails']->suburb}}">
                        <label for="suburb">Suburb*</label>
                    </div>
                </div>

                
                <div class="col s6">
                    <div class="input-field">
                        <input id="postalCode" name="postalCode" type="text" class="postalCode validate" value="{{$beauticianDetail['beauticianDetails']->zipcode}}">
                        <label for="postalCode">Postcode</label>
                    </div>
                </div>
                
            </div>



            <div class="row">

                <div class="col s6">
                    <div class="input-field">
                        <select id="state" name="state" type="text" class="state validate" value="{{$beauticianDetail['beauticianDetails']->state}}">
                            @foreach($states as $state)
                              <option value="{{$state}}" {{$state==$beauticianDetail['beauticianDetails']->state?'selected':''}}>{{$state}}</option>
                            @endforeach
                        </select>
                        <label for="state" class="state-label">State*</label>
                    </div>
                </div>

                <div class="col s6">
                    <div class="input-field">
                        <input id="country" name="country" type="text" class="country" readonly value="Australia">
                        <label for="country">Country*</label>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col s12">
                    <div class="input-field">
                        <input id="phone" name="phone" type="text" class="phone validate" value="{{$beauticianDetail['beauticianDetails']->phone_number}}">
                        <label for="phone">Mobile</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="input-field">
                        <input id="instaId" name="instaId" type="text" class="instaId validate" value="{{$beauticianDetail['beauticianDetails']->instagram_link}}">
                        <label for="instaId">Instagram ID</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="input-field">
                        <input id="password" type="password" class="validate" value="********">
                        <label for="password">Password</label>
                        <a href="#edit-password-modal" class="edit-password-link">Change Password</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m12 l12 xl6">
                    <div class="check-label">Do you offer cruelty free products?</div>
                    <div class="checkboxGroup">
                        <input type="checkbox" name="crueltyFreeMakeup" id="crueltyFreeMakeup" class="checks" value="1" @if($beauticianDetail[ 'beauticianDetails']->cruelty_free_makeup) checked @endif />
                        <label for="crueltyFreeMakeup" class=""></label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m12 l12 xl6">
                    <div class="check-label">Do you offer mobile services?</div>
                    <div class="checkboxGroup">
                        <input type="checkbox" id="radiusCheck" name="mobileServices" class="checks radiusCheck" @if($beauticianDetail[ 'beauticianDetails']->mobile_services == 1) checked @endif />
                        <label for="radiusCheck" class=""></label>
                    </div>
                </div>
                <div class="col s12 m12 l12 xl6">
                    <div class="form-field-wrapper">
                        <div class="d-d-wrapper-left radius-div @if($beauticianDetail[ 'beauticianDetails']->mobile_services == 0) disable-click @endif">
                            <span class="label-name">Travel Radius</span>
                            <div class="drop-down">
                                <div class="input-field">
                                    <div class="handle-counter" id="handleCounter">
                                        <button class="counter-minus btn btn-primary">-</button>
                                        <input type="text" name="workRadius" class="{{$beauticianDetail[ 'beauticianDetails']->work_radius}}?'':'disabled'}}" value="{{$beauticianDetail[ 'beauticianDetails']->work_radius}}">
                                        <button type="button" class="counter-plus btn btn-primary">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 padding-bottom-90">
                    <button type="submit" class="bg-btn waves-effect set-btn">Save</button>
                </div>
            </div>
        </div>
    </form>
</section>
<form method="POST" action="{{url('beautician/change-password')}}" id="changePassword" class="changePassword" />
<div id="edit-password-modal" class="modal">
    <div class="modal-content">
        <h4>Edit Password</h4>
        <div class="row">
            <div class="col s12">
                <div class="input-field">
                    <input id="oldPassword" type="password" name="oldPassword" class="oldPassword validate" maxlength="12">
                    <label for="oldPassword">Old Password</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <div class="input-field">
                    <input id="password" type="password" name="password" class="password validate" maxlength="12">
                    <label for="password">New Password</label>
                </div>
            </div>
        </div>
        <div class="row margin-bottom-none">
            <div class="col s12">
                <div class="input-field">
                    <input id="confirmPassword" type="password" name="confirmPassword" class="confirmPassword validate" maxlength="12">
                    <label for="confirmPassword">Confirm Password</label>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect border-btn">Cancel</a>
        <button type="submit" class="modal-action bg-btn waves-effect">Save</button>
        <!--<a href="#!" class="modal-action modal-close bg-btn waves-effect">Save</a>-->
    </div>
</div>
</form>
@stop @section('scriptjs')
<script src="{{URL::asset('assets/beautician/js/jquery.validate.js') }}"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/edit-profile.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/spinner.js')}}"></script>
@stop