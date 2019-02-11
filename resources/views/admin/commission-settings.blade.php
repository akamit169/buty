@extends('admin.layout.default_layout')
@section('title') {{{ 'Booked Service List' }}} @parent @stop {{-- Content --}}
@section('content')
<style type="text/css">
  
    #premium-beautypro-list
    {
        margin-top: 50px;
    }

    #premium-beautypro-list li
    {
        padding: 20px 10px;
    }

    #data-wrapper
    {
      margin-top: 20px;
    }

    .custom-select
    {
      display: inline-block;
      vertical-align: middle;
    }

    .premium-beautypro-businessname
    {
      display: inline-block;
      width: 200px;
    }

    #premium-beautypro-list li button
    {
      margin-right: 15px;
    }

    .modal-body span
    {
      display: inline-block;
      min-width: 100px;
    }

    .modal-body input
    {
      width:60px;
    }

    #commission-form span
    {
      display: inline-block;  
      padding-right:10px;
      font-weight: bold;
      min-width: 150px;
    }

    #commission-form button[type=submit]
    {
      margin-top: 20px;
    }

    #commission-form input
    {
      width: 60px;
    }

    #premium-beautypro-percent
    {
      margin-right: 60px;
    }

   

</style>

<link rel="stylesheet" type="text/css" href="{{URL::asset('assets/admin/css/jquery-customselect-1.9.1.css')}}" />

<div class="loader"><i></i></div>
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
                        <p><strong>Commission Fee Settings</strong></p>
                        <ul class="breadcrumb pull-right mr-t-7"> 
                            <li><a href="{{url('')}}"><i class="icon icon-home"></i> Home</a></li> 
                            <li class="active">Commission Fee Settings</li> 
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
                               <div class="commision-main-nav">
                                    <button id="premium-beautypro-percent" class="btn btn-default">Premium Beauty Pro Percentage</button>
                                    <div class="btn-group" role="group" aria-label="...">
                                      <button id="global-percent-btn" class="btn btn-default">Beauty Pro Global Percentage</button>
                                      <button id="service-percent-btn" class="btn btn-default">Service Percentage</button>
                                      <button id="state-percent-btn" class="btn btn-default">State Percentage</button>
                                    </div>
                               </div>

                                <div id="data-wrapper">
                              
                                  
                                </div>


                                   <div id="global-percent-button-modal" class="modal fade" tabindex="-1" role="dialog">
                                          <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                              <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title">Set Global percentage</h4>
                                              </div>

                                              <form>
                                              <div class="modal-body">
                                                     
                                                        
                                                      
                                                  </div>
                                                  <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-secondary">Save</button>
                                                  </div>

                                              </form>

                                            </div><!-- /.modal-content -->
                                          </div><!-- /.modal-dialog -->
                                     </div><!-- /.modal -->


                                     <div id="service-wise-percent-button-modal" class="modal fade" tabindex="-1" role="dialog">
                                          <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                              <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title">Set Services percentage</h4>
                                              </div>

                                              <form>
                                              <div class="modal-body">
                                                     
                                                  
                                                      
                                              </div>
                                              <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-secondary">Save</button>
                                              </div>

                                              </form>

                                            </div><!-- /.modal-content -->
                                          </div><!-- /.modal-dialog -->
                                     </div><!-- /.modal -->
                                   
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
<script src="{{URL::asset('assets/admin/js/jquery-customselect-1.9.1.min.js')}}"></script>
<script src="{{URL::asset('assets/admin/js/commission-settings.js')}}"></script>
@stop
@stop
