@extends('beautician.main-layout.home-layout')
@section('title'){{'Beauty Junkie'}}@endsection
@section('extendcss') @endsection
@section('content')
            <div class="error-msg-div"></div>
            <header>
                <div class="logo logo-center"></div>
                <a href="signup" class="sign-btn"><span>Sign Up</span></a>
            </header>
            <!-- start center container -->
            <div class="inner-container">
                <div class="login-screen">
                    <!-- start logo -->
                    <div class="logo-wrapper">
                        <div class="center-heading">Login</div>
                    </div>
                    <!-- end logo -->
                    <div class="large-btn-wrapper">
                        <form action="login" method="post">
                            {!! csrf_field() !!}
                            <div class="login-form">
                                <div class="input-field">
                                    <input id="email" type="text" name="email" class="validate" value="{{old('email')}}" autocomplete="off">
                                    <label for="email">Email</label>
                                </div>
                                <div class="input-field">
                                    <input id="password" type="password"  autocomplete="new-password" name="password" class="validate">
                                    <label for="password">Password</label>
                                </div>
                            </div>
                            <div class="forgot-link"><a href="forgot-password">Forgot Password?</a></div>
                            <button class="bg-btn waves-effect">Lets do this</button>
                            <a href="signup" class="waves-effect margin-none border-btn">Don't have an account? <span class="text-underline">Sign Up</span></a>
                        </form>
                    </div>
                </div>

            </div>

       @endsection
@section('scriptjs') 
@if(Session::get('success_msg')) 
        <script type="text/javascript">showValidationError('{{Session::get("success_msg")}}');</script>
        @elseif(Session::get('error_msg')) 
        <script type="text/javascript">showValidationError('{{Session::get("error_msg")}}');</script>
        @endif

        @if (isset($errors) && $errors->any())
        @foreach($errors->all() as $key=>$message)
        <script type="text/javascript">showValidationError('<?php echo $message; ?>');</script>
        @break;
        @endforeach
        @endif
@endsection


