@extends('admin.layout.default_layout')
@section('title') {{'Admin Login'}}  @parent @stop {{-- Content --}}
@section('content')
<div class="login-bg">
<section id="content" class="m-t-lg wrapper-md animated fadeInUp">
    <div class="container aside-xxl">
        <a class="navbar-brand block" href="#">
            <img src="{{URL::asset('assets/admin/images/logo.png')}}" class="" alt="">
            <span class="textLogo"> Beauty Junkie </span>
        </a> 
        <section id="signIn" class="panel panel-default bg-white m-t-lg animated fadeInUp">
            <header class="panel-heading text-center"> 
                <strong>Change Password</strong> 
            </header>
            <form action="{{url('admin/user/change-password')}}" id="admin_login" class="panel-body wrapper-lg admin_login" method="post">
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


                <div class="form-group"> 
                    <label>Current Password</label> 
                    <div class="">
                        <input type="password" value="" class="form-control tb-big band_name" name="old_password" autocomplete="new-password">
                    </div>
                </div>
                <div class="form-group"> 
                    <label>New Password</label> 
                    <div class="">
                        <input type="password" value="" class="form-control tb-big band_name" name="password" 
                        autocomplete="new-password">
                    </div>
                </div>
                <div class="form-group"> 
                    <label>Confirm Password</label> 
                    <div class="">
                        <input type="password" value="" autocomplete="new-password" class="form-control tb-big band_name" name="password_confirmation">
                    </div>
                </div>

                <button type="submit" class="btn btn-s-md bg-green">Done</button>
                <button type="button" class="btn btn-s-md bg-green m-l-10"  onclick="window.location ='{{ url("auth/login") }}'">Cancel</button>


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