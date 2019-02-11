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
                <div class="round-check"><img src="{{asset('assets/beautician/images/waiting-icon.png')}}" alt=""> </div>

                <div class="success-text">
                    <h4>Account review in progress</h4>
                    <p>Our team is currently reviewing your application. You will be notified via email once your application is successful. This usually takes around 24-48 hours.</p>
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