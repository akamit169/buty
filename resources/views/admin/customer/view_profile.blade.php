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
                        <p><strong>View Client Profile</strong></p>
                        <ul class="breadcrumb pull-right mr-t-7"> 
                           <li><a href="{{url('admin/user')}}"><i class="fa fa-home"></i> Home</a></li> 
                           <li class="active">Client Profile</li>
                        </ul>
                    </header> 
                    <!-- End of Page Heading -->     

                    <section class="scrollable wrapper w-f"> 
                        <div class="main-container">

                                <div class="row">
                                    <div class="panel-body"> 
                                        <div class="form-group custom-fields">
                                            <label class="custom-label"><strong>Client Email</strong></label> <span class="custom-span">{{$userObj->email}}</span>
                                        </div>
                                        <div class="form-group custom-fields">
                                            <label class="custom-label"><strong>Client Name</strong></label>  <span class="custom-span">{{$userObj->first_name.' '.$userObj->last_name}}</span>
                                        </div>
                                        <div class="form-group custom-fields">
                                            <label class="custom-label"><strong>Client Address</strong></label>  
                                            <span class="custom-span">
                                                @if(!empty($userObj->address))
                                                    {{$userObj->address}}
                                                @else
                                                    -
                                                @endif    
                                            </span>
                                        </div>
                                        <div class="form-group custom-fields">
                                            <label class="custom-label"><strong>Client Suburb</strong></label> 
                                            <span class="custom-span">
                                                @if(!empty($userObj->suburb))
                                                    {{$userObj->suburb}}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>

                                         <div class="form-group custom-fields">
                                            <label class="custom-label"><strong>Client State</strong></label> 
                                            <span class="custom-span">
                                                @if(!empty($userObj->state))
                                                    {{$userObj->state}}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>

                                      
                                        <div class="form-group custom-fields">
                                            <label class="custom-label"><strong>Client Profile Pic</strong></label>  
                                            <span class="custom-span">
                                                @if(!empty($userObj->profile_pic))
                                                    <a href="{{env('S3_BUCKET_PATH').env('S3_BUCKET').env('USER_PROFILE_PIC_S3').$userObj->profile_pic}}" target="_blank">
                                                    <img src="{{env('S3_BUCKET_PATH').env('S3_BUCKET').env('USER_PROFILE_PIC_S3').$userObj->profile_pic}}" height="50" width="50"/>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group custom-fields">
                                    <div class="custom-file-input">
                                        <a href="{{URL::previous()}}" class="btn btn-s-md green-button">Back</a>
                                       
                                    </div>
                                </div>
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
