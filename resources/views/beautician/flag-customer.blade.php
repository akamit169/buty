@extends('beautician.main-layout.home-layout')
@section('title'){{'Flag Client'}}@endsection
@section('extendcss') @endsection
@section('content')
            <div class="error-msg-div"></div>
<!-- start center container -->
<div class="container">
    <div class="center-heading">Select reason to flag the client</div>
    <div class="form-container">
    <form action="{{url('beautician/flagCustomer')}}" method="post">
      <input type="hidden" name="flaggedUser" value="{{\Request::input('id')}}">
      {{ csrf_field() }}
      
      <div class="col s12 m12 reasonDiv">
                        <div class="lage-label">
                            Select Reason
                        </div>
                        <div class="form-fields">
                            <div class="drop-down">
                                <div class="input-field">
                                    <select name="reasonId">
                                        @foreach($reasons as $key=>$value)
                                            @if($key == 0)
                                                <option value="{{$value['id']}}" selected>{{$value['reason']}}</option>
                                            @else
                                                <option value="{{$value['id']}}">{{$value['reason']}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="b-button">
                    <button class="bg-btn waves-effect" type="submit">Submit</button>
                </div>
      </form>
    </div>
</div>
@endsection
@section('scriptjs') 
        @if(Session::get('success_msg')) 
        <script type="text/javascript">showValidationError('{{Session::get("success_msg")}}');</script>
        @elseif(Session::get('error_msg')) 
        <script type="text/javascript">showValidationError('{{Session::get("error_msg")}}');</script>
        @endif

        @if (isset($errors) && $errors->any())
        @foreach($errors->all() as $key=>$message)
        <script type="text/javascript">showValidationError('<?php echo $message; ?>');</script>
        @break;
        @endforeach
        @endif
@endsection
