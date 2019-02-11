@extends('beautician.main-layout.main-layout') @section('title') {{'My Profile'}}@stop @section('content')
<link type="text/css" rel="stylesheet" href="{{asset('assets/beautician/css/slick.css')}}">
<link type="text/css" rel="stylesheet" href="{{asset('assets/beautician/css/slick-theme.css')}}">

<section class="inner-container top-padding">
    <div class="row">
     <div class="col s12 m6 sl-div">
  <div class="fade slider">
    <div>
      <img src="{{URL::asset('assets/beautician/images/img-1.jpg')}}">
      <p>Share, promote and <br> grow your portfolio</p>
    </div>
    <div>
      <img src="{{URL::asset('assets/beautician/images/img-2.jpg')}}">
      <p>Expand your client <br> base</p>
    </div>
    <div>
      <img src="{{URL::asset('assets/beautician/images/img-3.jpg')}}">
      <p>Manage your bookings <br> on the go</p>
    </div>
    <div>
      <img src="{{URL::asset('assets/beautician/images/img-4.jpg')}}">
      <p>With you on the road<br>or in the slaon</p>
    </div>
  </div>
  </div>
  </div>
</section>

@stop @section('scriptjs')
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/slick.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/tutorial.js')}}"></script>
@stop