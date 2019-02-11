@extends('admin.layout.default_layout')
@section('title') {{{ 'Analytics Report' }}} @parent @stop {{-- Content --}}
@section('content')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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
                    <header class="header bg-white b-b b-light"> 
                        <p><strong>Analytics Data</strong></p>
                        <ul class="breadcrumb pull-right mr-t-7"> 
                            <li><a href="{{url('admin/user')}}"><i class="fa fa-home"></i> Home</a></li> 
                            <li class="active">Analytics Data</li> 
                        </ul>
                    </header> 
                    <section class="scrollable wrapper w-f"> 
                        <div class="main-container">   
                            <form class="csv-form" action="" method="POST" enctype = 'multipart/form-data' > 

                                @if (isset($errors) && $errors->any())
                                <div class="alert alert-danger alert-dismissable server-error alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4><i class="icon fa fa-ban"></i> Error !</h4>
                                    @foreach($errors->all() as $key=>$message)
                                    <label class="error-msg">* {{$message}}</label><br/>
                                    @endforeach
                                </div>
                                @elseif (Session::has('status'))
                                <div class="alert alert-danger alert-dismissable server-error alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4><i class="icon fa fa-ban"></i> Error !</h4>
                                    <label class="text-success">{{Session::get('status')}}</label><br/>
                                </div>
                                @endif


                                @if(Session::get('error_msg')) 
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4><i class="icon fa fa-ban"></i> Error !</h4>
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
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="">
                                                        <b>Number of dancers who have completed sign up </b>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                        <div class="form-group"> 
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="">
                                                        <b>Number of dancers who have received 5 feedbacks </b>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                        <div class="form-group"> 
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="">
                                                        <b>Number of dancers who have received 25 feedbacks </b>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                        <div class="form-group"> 
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="">
                                                        <b>Which method of feedback collection is being used  </b>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                        <div class="form-group"> 
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="">
                                                        <b>Monthly active users(Receives at least 10 feedbacks a month) </b>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                        <div class="form-group"> 
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="">
                                                        <b>Number of dancers who have registered but haven’t received any feedback</b>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>



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


@stop
@stop
