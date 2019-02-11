@extends('admin.layout.default_layout')
@section('title') {{{ 'Disputed Bookings List' }}} @parent @stop {{-- Content --}}
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
                        <p><strong>Disputed Bookings</strong></p>
                        <ul class="breadcrumb pull-right mr-t-7"> 
                            <li><a href="{{url('')}}"><i class="icon icon-home"></i> Home</a></li> 
                            <li class="active">Disputes Bookings List</li> 
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
                                <div class="table-responsive">
                                    <table id="basicDataTable" class="table table-striped b-t margin-0 b-light">
                                        <thead class="custom-head">
                                            <tr>
                                                <th>Service Name</th>
                                                <th>Sub-Category Name</th>
                                                <th>Address</th>
                                                <th>Cost</th>
                                                <th>Time/Date From</th>
                                                <th>Time/Date To</th>
                                                <th>Beauty Pro Name</th>
                                                <th>Client Name</th>
                                                <th>Status</th>
                                                <th>Reason</th>
                                                <th>Action</th>
                                            </tr>
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
<script src="{{URL::asset('assets/admin/js/disputed_service_list.js')}}"></script>
@stop
@stop
