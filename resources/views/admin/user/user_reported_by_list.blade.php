@extends('admin.layout.default_layout')
@section('title') {{{ 'User Reported By List' }}} @parent @stop {{-- Content --}}
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
                        <p><strong>Users</strong></p>
                        <ul class="breadcrumb pull-right mr-t-7"> 
                            <li><a href="{{url('admin/user')}}"><i class="icon icon-home"></i> Home</a></li> 
                            <li class="active">User Reported By List</li> 
                        </ul>
                    </header> 
                    <!-- End of Page Heading -->     
                            
                    <section class="scrollable wrapper w-f"> 
                        <div class="main-container padd-bottom-70">
                            @if (count($errors) > 0)
                                <!-- Form Error List -->
                                <div class="alert alert-danger">
                                    <strong>Whoops! Something went wrong!</strong>
                                    <br><br>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @elseif(Session::has('message') && !Session::has('status'))
                                <div class="alert alert-danger text-left">
                                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                    <ul><li>{{ Session::get('message') }}</li></ul>
                                </div>
                            @elseif(Session::has('message') && Session::has('status'))
                                <div class="alert alert-success text-left">
                                    <ul><li>{{ Session::get('message') }}</li></ul>
                                </div>
                            @endif  
                            <section class="">
                                <div>
                                    <a href="{{URL::previous()}}" class="btn btn-success btn-sm">Back</a>
                                    <a href="{{url('admin/user/suspend-unsuspend-user/'.$arrUser[0]['id'])}}" class="btn btn-success btn-sm">Suspend User</a>
                                </div><br>
                                
                                <div class="table-responsive">
                                    <table id="basicDataTable" class="table table-striped b-t margin-0 b-light">
                                        <thead class="custom-head">
                                            <tr>
                                                <th>User Email</th>
                                                <th>Username</th>
                                                <th>User Type</th>
                                                <th>Reason</th>
                                                <th>Flagged On</th>
                                            </tr>
                                            
                                            @foreach($arrUser as $key=>$value)
                                            <tr>
                                                <td>{{$value['flagged_by_email']}}</td>
                                                <td>{{ucwords($value['flagged_by_first_name'])}}</td>
                                                <td>
                                                    @if($value['flagged_by_user_type'] == 2)
                                                        Beautician
                                                    @else
                                                        Client
                                                    @endif
                                                </td>
                                                <td>{{$value['reason']}}</td>
                                                <td>{{date('m/d/Y', strtotime($value['flagged_on']))}}</td>
                                            </tr>
                                            @endforeach
                                        </thead>
                                        
                                    </table>
                                </div>
                            </section>
                        </div>
                    </section>

                </section> 
            </section>
        </section>
    </section>
</section>
<!-- /#page-wrapper -->
<!--END PAGE WRAPPER-->
@section('scriptjs')
<!--<script src="{{URL::asset('assets/admin/js/user_reported_by_list.js')}}"></script>-->
@stop
@stop
