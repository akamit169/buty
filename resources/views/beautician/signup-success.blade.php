@extends('beautician.main-layout.home-layout')
@section('title'){{'Beauty Junkie'}}@endsection
@section('extendcss') @endsection
@section('content')
<header>
    <div class="logo logo-center"></div>
    <a href="login" class="login-btn">Log In</a>
</header>
<!-- start center container -->
<div class="inner-container">
   <div class="login-screen">
    <div class="large-btn-wrapper">
        <div class="center-heading">Almost Done</div>
        <div class="round-check"><img src="{{asset('assets/beautician/images/round-check.png')}}" alt="Site logo"> </div>

        <div class="success-text">
            <h4>Account Submitted for Review</h4>
            <p>Thanks for submitting your application. The team at beauty junkie will review your information and notify you via email once your account has been approved. This usually takes around 24-48 hours.</p>
            <br>
            <br>
        </div>


        <a href="{{url('beautician/')}}"><button class="bg-btn waves-effect">Ok</button></a>

    </div>
    </div>
</div>

@endsection
@section('scriptjs') 

@endsection
