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
                <div class="center-heading">Rejected</div>
                <div class="round-check"><img src="{{asset('assets/beautician/images/reject-icon.png')}}" alt=""> </div>

                <div class="success-text">
                    <h4>Account not approved</h4>
                    <p>Your application has unfortunately not been approved at this stage. Please contact the beauty junkie team to discuss your application further. Thanks!</p>
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