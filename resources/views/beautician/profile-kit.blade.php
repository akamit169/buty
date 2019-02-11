@extends('beautician.main-layout.main-layout')
@section('title') {{'My Kit'}}@stop
@section('content')

<div class="upload-work-wrap top-padding">
    <div class="kit-form">
        <div class="input-field">
            <p>My Kit</p>
            <input placeholder="Type here" id="kitName" type="text" class="kitName validate alphaonly">
        </div>
        <div class="kit-list">
            <ul class="kitNameList">
                @if(count($beauticianKit) > 0)
                    @foreach($beauticianKit as $value)
                    <li>
                        <span class="icon icon-delete"></span> {{ucwords($value['kit_name'])}}
                        <input type="hidden" name="kitNameId" value="{{$value['id']}}" />
                    </li>
                    @endforeach
                @endif
            </ul>
        </div>
        <div class="upload-btn">
            <button class="bg-btn waves-effect sbmKit">Save</button>
        </div>
    </div>
</div>
@stop
@section('scriptjs')
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/profile-kit.js')}}"></script>
@stop

