@extends('beautician.main-layout.home-layout')
@section('title'){{'Beauty Junkie'}}@endsection
@section('extendcss') @endsection
@section('content')

    <div class="inner-container">
      <div class="round-check"><img src="{{asset('assets/beautician/images/round-check.png')}}" alt=>  </div>

          <div class="success-text">
            <h4>Success</h4>
            <p>Password changed successfully.</p>  <br><br>
          </div>
          
    </div>

@endsection