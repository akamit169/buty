@extends('beautician.main-layout.home-layout')
@section('title'){{'Beauty Junkie'}}@endsection
@section('extendcss') @endsection
@section('content')
<div class="error-msg-div"></div>
<header>
    <a href="signup" class="sign-btn"><span>Sign Up</span></a>
    <div class="logo"></div>
    <a href="login" class="login-btn">Log In</a>
</header>
<!-- start center container -->
<div class="inner-container">
    <div class="login-screen">
        <!-- start logo -->
        <div class="logo-wrapper">
            <div class="center-heading">Forgot Password</div>
        </div>
        <!-- end logo -->
        <div class="large-btn-wrapper">
            <form action="forgot-password" method="post">
            {!! csrf_field() !!}
            <div class="login-form">
                <div class="input-field">
                     <input id="email" type="text" name="email" value="{{old('email')}}" class="validate" autocomplete="off" >
                     <label for="email">Email </label>
                </div>
            </div>
                <button class="bg-btn waves-effect btn-bottom-align" type="submit">Send</button>
            </form>
        </div>
    </div>

</div>
@endsection
@section('scriptjs') 
@if (isset($errors) && $errors->any())
@foreach($errors->all() as $key=>$message)
<script type="text/javascript">showValidationError('<?php echo $message; ?>');</script>
@break;
@endforeach
@endif

@if(Session::get('error_msg')) 
<script type="text/javascript">showValidationError('{{Session::get("error_msg")}}');</script>
@endif
@endsection
