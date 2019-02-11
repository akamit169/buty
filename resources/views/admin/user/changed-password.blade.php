@extends('admin.layout.default_layout')
@section('title') {{{ 'Users List' }}} @parent @stop {{-- Content --}}
@section('content')

<!--BEGIN PAGE WRAPPER-->
<section class="vbox">
    @include('admin.layout.menu_header')
    <section>
        <section class="hbox stretch">
            <!-- .aside --> 
            @include('admin.layout.sidebar')
            <!-- /.aside --> 
            <section id="content"> 
                <section class="vbox"> 
                    <!-- Page Heading -->
                    <header class="header bg-white b-b b-light"> 
                        <p><strong>Change Password</strong></p>
                        <ul class="breadcrumb pull-right mr-t-7"> 
                           <li><a href="{{url('admin/user')}}"><i class="fa fa-home"></i> Home</a></li> 
                           <li class="active">Change Password</li>
                        </ul>
                    </header> 
                    <!-- End of Page Heading -->     

                    <section class="scrollable wrapper w-f"> 
                        <div class="main-container">
                            <form action="{{url('admin/user/change-password-by-dasboard')}}" id="admin_login" class="stream-form change_password" method="post">


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

                                <div class="row">
                                    <div class="panel-body"> 
                                        <div class="form-group"> 
                                            <label>Current Password</label> 
                                            <div class="">
                                                <input type="password" value=""  autocomplete="new-password" class="form-control tb-big band_name" name="old_password">
                                            </div>
                                        </div>
                                        <div class="form-group"> 
                                            <label>New Password</label> 
                                            <div class="">
                                                <input type="password" autocomplete="new-password" value="" class="form-control tb-big band_name" name="password">
                                            </div>
                                        </div>
                                        <div class="form-group"> 
                                            <label>Confirm Password</label> 
                                            <div class="">
                                                <input type="password" autocomplete="new-password" value="" class="form-control tb-big band_name" name="password_confirmation">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group"> 
                                    <div class="custom-file-input">
                                        <button type="submit" class="btn btn-s-md green-button">Save</button>
                                       
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="mar-b-40"></div>
                    </section>

                </section> 
            </section>
        </section>
    </section>
</section>
<!-- /#page-wrapper -->
<!--END PAGE WRAPPER-->
@section('admin.layout.footer')
<!--<script src="{{URL::asset('admin-panel/assets/js/user-list.js')}}"></script>-->
@stop
@stop
