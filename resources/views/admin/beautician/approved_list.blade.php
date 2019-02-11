@extends('admin.layout.default_layout')
@section('title') {{{ 'Beauty Pro List' }}} @parent @stop {{-- Content --}}
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
                        <p><strong>Approved Beauty Pro List</strong></p>
                        <ul class="breadcrumb pull-right mr-t-7"> 
                            <li><a href="{{url('admin/user')}}"><i class="icon icon-home"></i> Home</a></li> 
                            <li class="active">Approved Beauty Pro List</li> 
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
                                <div><button class="btn btn-success btn-sm" id="export-user-data">Export Data</button></div><br>
                                <div class="table-responsive">
                                    <table id="basicDataTable" class="table table-striped b-t margin-0 b-light">
                                        <thead class="custom-head">
                                            <tr>
                                                <th>Beauty Pro Email</th>
                                                <th>Beauty Pro Name</th>
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
<script src="{{URL::asset('assets/admin/js/approved_beautician_list.js')}}"></script>
@stop
@stop
