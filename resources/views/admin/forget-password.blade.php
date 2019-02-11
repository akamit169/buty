@extends('admin.layout.default_layout')
@section('title') {{'Forgot Password'}}  @parent @stop {{-- Content --}}
@section('content')
<div class="login-bg">
<section id="content" class="m-t-lg wrapper-md animated fadeInUp">
    <div class="container aside-xxl">
        <a class="navbar-brand block" href="#">

            <img src="{{URL::asset('assets/admin/images/logo.png')}}" class="" alt="">
            <span class="textLogo">beauty junkie</span>
        </a> 


        <section id="forgotPassword" class="panel panel-default bg-white m-t-lg animated fadeInUp">
            <header class="panel-heading text-center"> <strong>Forgot password?</strong> </header>
            <form action="{{url('admin/user/email')}}" id="admin-forgot-password" class="panel-body wrapper-lg admin-forgot-password" method="post">
                {!! csrf_field() !!}

                @if (isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissable server-error alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-ban"></i> Error!</h4>
                    @foreach($errors->all() as $key=>$message)
                    <label class="error-msg">* {{$message}}</label><br/>
                    @endforeach
                </div>
                @elseif (Session::has('status'))
                <div class="alert alert-danger alert-dismissable server-error alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-ban"></i> Error!</h4>
                    <label class="text-success">{{Session::get('status')}}</label><br/>
                </div>
                @endif


                @if(Session::get('error_msg')) 
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-ban"></i> Error!</h4>
                    {{Session::get('error_msg')}}
                </div>
                @elseif(Session::get('success_msg'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Success !</h4>
                    {{Session::get('success_msg')}}
                </div>
                @endif

                <input type="hidden" name="user_type" id="user_type" class="user_type" value="1" />
                <div class="form-group"> 
                    <label class="control-label">Email</label> 
                    <input type="email" name="email" id="forgot-email" placeholder="Please enter email" class="form-control input-lg forgot-email"> 
                </div>
                <a href="{{url('auth/login')}}" class="pull-right m-t-xs">
                    <small>Back</small>
                </a> 
                <button type="submit" class="btn btn-s-md btn-login" id="forgot-password">
                    <b>Send</b>
                </button>
            </form>
        </section> 

    </div>
</section>
<!-- footer --> 
<footer id="footer">
    <div class="text-center padder">
        <p> <small></small> </p>
    </div>
</footer>
@section('admin.layout.footer')
</div>
<script>$('html').addClass('bg-green');</script>
@stop
@stop