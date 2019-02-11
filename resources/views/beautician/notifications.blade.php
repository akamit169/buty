@extends('beautician.main-layout.main-layout')
@section('title') {{'Notifications'}}@stop
@section('content')      

        <!-- start content area -->
        <section class="inner-container content-area notification-wrapper">
           <div class="row margin-bottom-none">
                <div class="col s4 m4 list-for-mobile">
                    <div class="all-notification">
                            <ul class="notification-items">
                            </ul>
                            <div class="ajax-loader">
                                <img src="{{URL::asset('assets/beautician/images/loading.gif')}}">
                            </div>
                        </div>
                </div>
                <div class="col s8 m8 detail-for-mobile">
                    <div class="main-content-area notification-details">
                    </div>
                </div>
            </div>
        </section>
        <footer>
            <div class="footer-container">
                <a href="terms-and-conditions.html" class="tc-div">Terms &amp; Conditions</a>
                <a href="privacy-policy.html" class="privacy-div">Privacy Policy</a>
            </div>
        </footer>
    </div>
    <!-- /end container -->


@stop
@section('scriptjs')
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/notification.js')}}"></script>
@stop