@extends('beautician.main-layout.main-layout') @section('title') {{'My Profile'}}@stop @section('content')

<section class="inner-container top-padding">
    <form method="POST" id="beauticianPaymentDetail" class="beauticianPaymentDetail" action="{{url('beautician/setting/beauticianPaymentDetail')}}" enctype="multipart/form-data">
        {!! csrf_field() !!} @if (isset($errors) && $errors->any())
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
        <div class="payment-details">

            <div class="row">
                <div class="col s12 m12 l12 xl6">
                    <div class="row">
                        <div class="col s12 section-heading">Banking Details</div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                            <div class="input-field">
                                <input name="bankFirstName" type="text" class="validate" @if(!empty($bankDetail)) value="{{$bankDetail->legal_entity->first_name}}" @endif>
                                <label for="bankFirstName">First Name</label>
                            </div>
                        </div>
                        <div class="col s12">
                            <div class="input-field">
                                <input id="bankLastName" name="bankLastName" type="text" class="validate" @if(!empty($bankDetail)) value="{{$bankDetail->legal_entity->last_name}}" @endif>
                                <label for="bankLastName">Last Name</label>
                            </div>
                        </div>

                         <div class="col s12">
                            <div class="input-field">
                                <input id="dob" name="dob" type="text" class="validate" @if(!empty($bankDetail) && !empty($bankDetail->legal_entity->dob->day)) value="{{$bankDetail->legal_entity->dob->day}}-{{$bankDetail->legal_entity->dob->month}}-{{$bankDetail->legal_entity->dob->year}}" @endif>
                                <label for="dob">Date of Birth (dd-mm-yyyy)</label>
                            </div>
                        </div>

                        <div class="col s12">
                            <div class="input-field">
                                <input id="bsb" name="bsb" type="text" maxlength="6" class="validate" @if(!empty($bankDetail)) value="{{str_replace(" "," ",$bankDetail->external_accounts['data'][0]['routing_number'])}}" @endif>
                                <label for="bsb">BSB</label>
                            </div>
                        </div>
                        <div class="col s12">
                            <div class="input-field">
                                <input name="accountNo" type="text" maxlength="9" class="validate" @if(!empty($bankDetail)) value="{{$bankDetail->external_accounts['data'][0]['last4']}}" @endif>
                                <label for="accountNo">Account No.</label>
                            </div>
                        </div>
                        <div class="col s12 pay-upload">
                            <div class="upload-lagel">
                                <div class="police-certificate" style="display:none;">
                                    <div class="uploaded-certificate">
                                        <i class="cross-icon"></i>
                                    </div>
                                </div>
                                <div class="upload-btn-wrap" style="display:block;">
                                    <span>Upload an image of an identifying document, such as a passport or driver’s license</span>
                                    <input type="file" name="certificate" id="profile-img" accept="image/*">
                                    <i class="upload-icon"></i>
                                </div>

                            </div>
                            <label class="note-sp">*Only jpeg/png files are allowed </label>
                        </div>
                    </div>
                </div>
                <div class="col s12 m12 l12 xl6">
                    <div class="row">
                        <div class="col s12 section-heading">Credit Card Details</div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                            <div class="input-field">
                                @if(!empty($cardDetail))
                                <input id="cardName" data-stripe="name" type="text" class="validate" placeholder="" value="{{$cardDetail->name}}"> @else
                                <input id="cardName" data-stripe="name" type="text" class="validate" placeholder=""> @endif
                                <label for="cardName">Name on Card</label>
                            </div>
                        </div>
                        <div class="col s12">
                            <div class="input-field">
                                @if(!empty($cardDetail))
                                <input id="cardNumber" maxlength="16" data-stripe="number" type="text" class="validate" value="XXXXXXXXXXXX{{$cardDetail->last4}}"> @else
                                <input id="cardNumber" maxlength="16" data-stripe="number" type="text" class="validate" placeholder=""> @endif
                                <label for="cardNumber">Card Number</label>
                            </div>
                        </div>
                        <div class="col s12">
                            <div class="input-field">
                                @if(!empty($cardDetail))
                                <input id="expiryMonth" data-stripe="exp_month" maxlength="2" type="text" class="validate" placeholder="MM" @if($cardDetail->exp_month
                                < 10) value="0{{$cardDetail->exp_month}}" @else value="{{$cardDetail->exp_month}}" @endif>
                                    @else
                                    <input id="expiryMonth" data-stripe="exp_month" maxlength="2" type="text" class="validate" placeholder="MM"> @endif
                                    <label for="expiry">Expiry Month</label>
                            </div>
                        </div>
                        <div class="col s12">
                            <div class="input-field">
                                @if(!empty($cardDetail))
                                <input id="expiryYear" data-stripe="exp_year" maxlength="2" type="text" class="validate" placeholder="YY" value="{{substr($cardDetail->exp_year, -2)}}"> @else
                                <input id="expiryYear" data-stripe="exp_year" maxlength="2" type="text" class="validate" placeholder="YY"> @endif
                                <label for="expiry">Expiry Year</label>
                            </div>
                        </div>
                        <div class="col s12">
                            <div class="input-field">
                                <input id="cvc" maxlength="3" data-stripe="cvc" type="text" class="validate" placeholder="XXX">
                                <label for="cvc">CVV</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 padding-bottom-90">
                    <button class="bg-btn waves-effect set-btn" id="sbmBtn">Save</button>
                </div>
            </div>
        </div>
    </form>
</section>
@stop @section('scriptjs')
<script src="{{URL::asset('assets/beautician/js/jquery.validate.js') }}"></script>
<script src="https://js.stripe.com/v2/"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/jquery-ui.min.js')}}"></script>
<script>
    Stripe.setPublishableKey("<?php echo env('STRIPE_KEY') ?>");
</script>
<script src="{{URL::asset('assets/beautician/js/payment-detail.js') }}"></script>
@stop