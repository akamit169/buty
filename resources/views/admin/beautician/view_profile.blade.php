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
                        <p><strong>View Beauty Pro Profile</strong></p>
                        <ul class="breadcrumb pull-right mr-t-7"> 
                           <li><a href="{{url('admin/user')}}"><i class="fa fa-home"></i> Home</a></li> 
                           <li class="active">Beauty Pro Profile</li>
                        </ul>
                    </header> 
                    <!-- End of Page Heading -->     

                    <section class="scrollable wrapper w-f"> 
                        <div class="main-container">

                                <div class="row">
                                    <div class="panel-body"> 
                                        <div class="form-group custom-fields"> 
                                            <label class="custom-label"><strong>Beauty Pro Email</strong></label> <span class="custom-span">{{$userObj->email}}</span>
                                        </div>
                                        <div class="form-group custom-fields"> 
                                            <label class="custom-label"><strong>Beauty Pro Name</strong></label>  <span class="custom-span">{{$userObj->first_name.' '.$userObj->last_name}}</span>
                                        </div>
                                        <div class="form-group custom-fields"> 
                                            <label class="custom-label"><strong>Beauty Pro Phone Number</strong></label>  
                                            <span class="custom-span">
                                                @if(!empty($userObj->phone_number))
                                                    {{$userObj->phone_number}}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>
                                        <div class="form-group custom-fields">
                                            <label class="custom-label"><strong>Beauty Pro ABN</strong></label>  <span class="custom-span">{{$userObj->abn}}</span>
                                        </div>
                                        <div class="form-group custom-fields"> 
                                            <label class="custom-label"><strong>Beauty Pro Business Name</strong></label>  <span class="custom-span">{{$userObj->business_name}}</span>
                                        </div>
                                        <div class="form-group custom-fields"> 
                                            <label class="custom-label"><strong>Beauty Pro Address</strong></label>  
                                            <span class="custom-span">
                                                @if(!empty($userObj->address))
                                                    {{$userObj->address}}
                                                @else
                                                    -
                                                @endif    
                                            </span>
                                        </div>
                                        <div class="form-group custom-fields"> 
                                            <label class="custom-label"><strong>Beauty Pro Suburb</strong></label>  
                                            <span class="custom-span">
                                                @if(!empty($userObj->suburb))
                                                    {{$userObj->suburb}}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>

                                         <div class="form-group custom-fields">
                                            <label class="custom-label"><strong>Beauty Pro State</strong></label> 
                                            <span class="custom-span">
                                                @if(!empty($userObj->state))
                                                    {{$userObj->state}}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>

                                        <div class="form-group custom-fields"> 
                                            <label class="custom-label"><strong>Beauty Pro Instagram Link</strong></label>  
                                            <span class="custom-span">
                                                @if(!empty($userObj->instagram_link))
                                                    {{$userObj->instagram_link}}
                                                @else
                                                    -
                                                @endif    
                                            </span>
                                        </div>
                                        <div class="form-group custom-fields"> 
                                            <label class="custom-label"><strong>Beauty Pro Approval Status</strong></label>  
                                            <span class="custom-span">
                                                @if($userObj->admin_approval_status == 0) 
                                                    Pending
                                                @elseif($userObj->admin_approval_status == 1)
                                                    Approved
                                                @else
                                                    Rejected
                                                @endif
                                            </span>
                                        </div>
                                        <div class="form-group custom-fields"> 
                                            <label class="custom-label"><strong>Beauty Pro Status</strong></label>  
                                            <span class="custom-span">
                                                @if($userObj->status == 0) 
                                                    In-Active
                                                @else
                                                    Active
                                                @endif
                                            </span>
                                        </div>
                                        <div class="form-group custom-fields"> 
                                            <label class="custom-label"><strong>Beauty Pro Profile Pic</strong></label>  
                                            <span class="custom-span">
                                                @if(!empty($userObj->profile_pic))
                                                    <a href="{{env('S3_BUCKET_PATH').env('S3_BUCKET').env('USER_PROFILE_PIC_S3').$userObj->profile_pic}}" target="_blank">
                                                    <img src="{{env('S3_BUCKET_PATH').env('S3_BUCKET').env('USER_PROFILE_PIC_S3').$userObj->profile_pic}}" height="50" width="50"/>
                                                @else
                                                    -
                                                @endif
                                                </a>
                                            </span>
                                        </div>
                                        <div class="form-group custom-fields"> 
                                            <label class="custom-label"><strong>Beauty Pro Police Check Certificate</strong></label>  
                                            <span class="custom-span">
                                                <a href="{{env('S3_BUCKET_PATH').env('S3_BUCKET').env('REPORT_IMAGES_S3').$userObj->police_check_certificate}}" target="_blank">
                                                <img src="{{env('S3_BUCKET_PATH').env('S3_BUCKET').env('REPORT_IMAGES_S3').$userObj->police_check_certificate}}" height="50" width="50"/>
                                                </a>
                                            </span>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group"> 
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
