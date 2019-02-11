@extends('beautician.main-layout.home-layout')
@section('title'){{'Beauty Junkie'}}@endsection
@section('extendcss') @endsection
@section('content')
<div class="error-msg-div"></div>
<header>
    <div class="logo logo-center"></div>
    <a href="login" class="login-btn">Log In</a>
</header>
<!-- start center container -->
<div class="container">
    <div class="center-heading">Create Account</div>
    <div class="form-container">
        <form action="{{url('beautician/signup')}}" id="signupForm" method="post" enctype="multipart/form-data">
            {!! csrf_field() !!}
            <div class="row">
                <div class="col s12 m6">
                    <div class="input-field">

                        <input id="fName" type="text" name="firstName" class="validate" value="{{old('firstName')}}" autocomplete="off">
                        <label for="fName">First Name</label>
                    </div>
                </div>
                <div class="col s12 m6">
                    <div class="input-field">
                        <input id="lName" type="text" name="lastName" class="validate" value="{{old('lastName')}}" autocomplete="off">
                        <label for="lName">Last Name</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="input-field">
                        <input id="bName" type="text" name="businessName" class="validate" value="{{old('businessName')}}" autocomplete="off">
                        <label for="bName">Business Name</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m6">
                    <div class="input-field">
                        <input id="abn" name="abn" type="text" class="validate" value="{{old('abn')}}" maxlength="11" autocomplete="off">
                        <label for="abn">ABN</label>
                    </div>
                </div>
                <div class="col s12 m6">
                    <div class="input-field">
                        <input id="mobile" type="text" name="phone" class="validate" value="{{old('phone')}}" minlength="8" maxlength="15" autocomplete="off">
                        <label for="mobile">Mobile</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m6">
                    <div class="input-field">
                        <input id="email" type="text" name="email" class="validate" value="{{old('email')}}" autocomplete="new-password">
                        <label for="emailId">Email ID</label>
                    </div>
                </div>
                <div class="col s12 m6">
                    <div class="input-field">
                        <input id="instagramId" name="instaId" type="text" class="validate" value="{{old('instaId')}}" autocomplete="off">
                        <label for="instagramId">Instagram ID</label>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col s12 m6">
                    <div class="input-field">
                        <input id="password" type="password" name="password" class="validate" autocomplete="new-password" >
                        <label for="password">Password</label>
                    </div>
                    <div class="input-field">
                        <input id="confirmPassword" type="password" name="confirmPassword" class="validate">
                        <label for="confirmPassword">Confirm Password</label>
                    </div>
                </div>
                <div class="col s12 m6">
                    <div class="upload-lagel">

                        <div class="police-certificate" style="display:none;">
                            <div class="uploaded-certificate">
                                <i class="cross-icon"></i>

                            </div>    
                        </div>


                        <div class="upload-btn-wrap" style="display:block;">
                            <span>Upload Police Check Certificate</span>
                            <input type="file" name="certificate" id="profile-img" accept="image/*">
                            <i class="upload-icon"></i>
                        </div>

                    </div>
                </div>
            </div>
            <div class="large-btn-wrapper">
                <button id="submit_signup" class="bg-btn waves-effect" type="button">Create</button>
                <p class="login-link tc-link">
                        <input name="tcCheck" id="tcCheck" class="checks" type="checkbox">
               <span class="signup-condition"> By signing up you agree to the <a href="privacy-policy" target="_blank">Privacy Policy</a> and the <a href="terms-and-conditions" target="_blank">Terms & Conditions</a></span></p>
                <a class="waves-effect border-btn" href="login">Have an account? <span class="text-underline">Login</span></a>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scriptjs') 
<script type="text/javascript" src="{{asset('assets/beautician/js/upload-certificate.js')}}"></script>
@endsection
