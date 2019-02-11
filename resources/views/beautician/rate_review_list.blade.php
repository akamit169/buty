@extends('beautician.main-layout.main-layout') @section('title') {{'Client Rating'}}@stop @section('content')

<div class="inner-container">
    @if (isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissable server-error success-msg-div">
                <h4><i class="icon fa fa-ban"></i> Error !</h4>
                @foreach($errors->all() as $key=>$message)
                <label class="error-msg">* {{$message}}</label><br/>
                @endforeach
            </div>
    @elseif (Session::has('status'))
            <div class="alert alert-success server-error success-msg-div">
                <h4><i class="icon fa fa-ban"></i> Success !</h4>
                <label class="text-success">{{Session::get('status')}}</label><br/>
            </div>
    @endif
    @if(Session::get('error_msg')) 
            <div class="alert alert-danger alert-dismissable server-error success-msg-div">
                <h4><i class="icon fa fa-ban"></i> Error !</h4>
                {{Session::get('error_msg')}}
            </div>
    @elseif(Session::get('success_msg'))
            <div class="alert alert-success server-error success-msg-div">
                <h4><i class="icon fa fa-check"></i> Success !</h4>
                {{Session::get('success_msg')}}
            </div>
    @endif
    <div class="booking-wrapper padding-bottom-50">
        <div class="b-heading">Rate & Review</div>
        <div class="">
            <ul class="booking-list">
                @foreach($rating as $value)
                @if(!empty($value['first_name']))
                <li>
                    <div class="row b-l-user">
                        <div class="col s6">
                            <span class="b-l-user-pic">
                                @if(!empty($value['profile_pic']))
                                    <img src="{{$value['profile_pic']}}" alt="User">
                                @else
                                    <img src="{{URL::asset('assets/beautician/images/profile_default.jpg')}}" alt="User">
                                @endif
                            </span>
                            <span class="b-l-user-name">{{$value['first_name'].' '.$value['last_name']}}</span>
                        </div>
                        <div class="col s6">
                            <div class="rating">
                                    @for($i=1; $i<=$value['rating']; $i++)
                                    @if($i == 1)
                                        <span class="stars rated-stars">
                                    @endif    
                                        <i class="icon icon-star"></i>
                                    @if($i == $value['rating'])
                                        </span>
                                    @endif
                                    @endfor
                                    @if($value['rating']<5)
                                    <span class="stars">
                                        @for($i=$value['rating']+1; $i<=5; $i++)
                                            <i class="icon icon-star"></i>
                                        @endfor
                                    </span>    
                                    @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                            @if($value['rating'] > 3) 
                                {{$value['comment']}}
                            @else
                                {{$value['rating_reason']}}
                            @endif    
                        </div>
                    </div>
                </li>
                @endif
                @endforeach
            </ul>
            <div class="booking-form">
                <div class="b-button">
                    <!--<a href="{{url('beautician/rateReviewUser?bookingId='.$bookingId.'&userId='.$userId)}}" class="bg-btn waves-effect">Write a Review</a>-->
                </div>
            </div>
        </div>
    </div>
</div>
@stop @section('scriptjs')
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/spinner.js')}}"></script>
@stop