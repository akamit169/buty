@extends('beautician.main-layout.home-layout')
@section('title'){{'Beauty Junkie'}}@endsection
@section('extendcss') @endsection
@section('content')
            <header>
                <div class="logo logo-center"></div>
                <a href="{{url('beautician/login')}}" class="login-btn">Log In</a>
            </header>
            <div class="inner-container">
                <div class="banner-section">
                    <img src="{{asset('assets/beautician/images/banner.png')}}" alt="banner">
                    <div class="signup-section">Are you a Beauty Pro?
                        <br>Signup today to showcase your work
                        <br>
                        <a href="{{url('beautician/signup')}}" class="primary-btn">Sign Up</a>
                    </div>
                </div>
                <div class="tab-section home-tab">
                    <span>Browse Collections</span>
                    <div class="common-tabs">
                        <i class="drop-icon"></i>
                        <div class="active-tab">{{strtolower($topLevelServices[0]->name)}}</div>
                        <ul class="tabs">
                            <?php $i=0;?>
                            @foreach($topLevelServices as $mainService)
                               <li class="tab col s3"><a class="{{$i==0?'active':''}}" href="#service{{$mainService->id}}" id="{{$mainService->id}}" data-service="{{$mainService->name}}">{{strtolower($mainService->name)}}</a></li>
                               <?php $i++; ?>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="captions-wrap home-pg">
                    <div id="service{{$topLevelServices[0]->id}}">
                        <div class="row">
                            @if(count($services) > 0)
                            @foreach($services as $service)
                            <div class="col s6 m4 l3">
                                <div class="captions">
                                    <div class="img-box"><img src="{{$service['service_image']}}"></div>
                                    <div class="caption-details">
                                        <div class="profile-pic"><img src="{{$service['profile_image']}}"></div>
                                        <div class="user-details">
                                            <div class="name">{{strlen($service['business_name']) > 10 ? substr($service['business_name'], 0, 10).".." : $service['business_name']}}</div>
                                            <!--<div class="date">{{$service['created_date']}}</div>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>

                    @foreach($topLevelServices as $mainService)
                      <div id="service{{$mainService->id}}"></div>
                    @endforeach
                </div>
            </div>
                  @endsection
@section('scriptjs') 
       <script>
            
        $(window).load(function(){
           dateConvert(); 
        });    
        $(".tab").click(function () {
            $id = $(this).find('a').attr('id');
            $.ajax({
                type: "get",
                url: "{{url('beautician/getServiceImages')}}",
                data: {'service_id': $id},
                success: function (result) {
                    if (result) {
                        console.log(result);
                        $('#service' + $id).html(result);
                        dateConvert();
                    }
                }
            });

            
        });
        
        function dateConvert(){ 
            $('.date').each(function(index,val){
              $(this).html(convertToLocalDateTime($(this).html())) ;
            });
        }
        </script>
@endsection
 
