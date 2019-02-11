@extends('beautician.main-layout.home-layout')
@section('title'){{'Beauty Junkie'}}@endsection
@section('extendcss') @endsection
@section('content')

<?php //dd(count($errors));?>
<div class="error-msg-div"></div>

<!-- start center container -->
<div class="inner-container">
    <form action="reset-password" id="reset-password-form" method="post" >
        <input type="hidden" name="token" value="{{\Request::input('token')}}">
        {!! csrf_field() !!}
        <div class="login-screen">
            <!-- start logo -->
            <div class="logo-wrapper">
                <div class="center-heading">Reset Password</div>
            </div>
            <!-- end logo -->
            <div class="large-btn-wrapper">
                <div class="login-form">
                    <div class="input-field">
                        <input id="password" name="password" type="password" class="validate" autocomplete="new-password">
                        <label for="password">Password</label>
                    </div>
                    <div class="input-field">
                        <input id="confirmPassword" type="password" class="validate" name="confirmPassword" autocomplete="new-password">
                        <label for="confirmPassword">Confirm Password</label>
                    </div>
                </div>
                <button class="bg-btn waves-effect">Reset</button>
            </div>
        </div>
    </form>
</div>
@endsection
@section('scriptjs') 
@if (isset($errors) && count($errors)>0)
    @foreach($errors->all() as $key=>$message)
      <script type="text/javascript">showValidationError('<?php echo $message; ?>');</script>
      @break;
    @endforeach
@endif

@if(Session::get('error_msg')) 
<script type="text/javascript">showValidationError('{{Session::get("error_msg")}}');</script>
@endif

@if(!$success)
<script type="text/javascript">showValidationError('{{$message}}');</script>
@endif
@endsection
