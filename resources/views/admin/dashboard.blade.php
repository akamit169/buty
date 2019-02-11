@extends('admin.layout.default_layout')
@section('title') {{'Admin Dashboard'}}  @parent @stop {{-- Content --}}
@section('content')
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
                        <p><strong>Welcome to beauty junkie!</strong></p>
<!--                        <ul class="breadcrumb pull-right mr-t-7"> 
                            <li><a href="javascript:void(0);"><i class="icon icon-home"></i> Home</a></li> 
                            <li class="active"> <i class="icon icon-change-password"></i>Statistics</li> 
                        </ul>-->
                    </header>
                    
                    <section class="scrollable wrapper w-f"> 
                        <div class="row">
<!--                            <div class="col-sm-6">
                                <section class="panel panel-default">
                                    <header class="panel-heading head-bg-white text-center font-bold">Hours of music uploaded to date</header>
                                    <div class="panel-body text-center">
                                        <div class="stat-show"> 
                                            <span class="icon icon-to-date f-85"></span>
                                            <span class="dateTime" ></span>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <div class="col-sm-6">
                                <section class="panel panel-default">
                                    <header class="panel-heading head-bg-white text-center font-bold">New  flagged requests</header>
                                    <div class="panel-body text-center">
                                        <div class="stat-show"> 
                                            <span class="icon icon-flag "></span>
                                            <span title=""></span>
                                            <div class="btn-full">
                                                <a class="btn btn-s-md bg-green btn-rounded" href="">view flagged items</a>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>-->
                        </div>
                        <div class="mar-b-40"></div>
                    </section>
                    
                    <footer class="footer bg-white b-t b-light"> <p></p> </footer>

                </section> 
            </section>
        </section>
    </section>
</section>
@stop