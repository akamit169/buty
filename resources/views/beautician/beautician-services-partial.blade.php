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
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @endif
</div>