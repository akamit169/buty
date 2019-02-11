@extends('beautician.main-layout.main-layout')
@section('title') {{'My Expertise'}}@stop
@section('content')
<!-- start content area -->
<section class="inner-container top-padding">
    <div class="row">
        <div class="col s12">
            <div class="pro-section">
                <div class="row">
                    <div class="col s12 m6">
                        <div class="input-field">
                            <label class="active">Qualifications</label>
                            <input id="qualification" type="text" class="qualification validate alphaonly" name="qualification" placeholder="Type here">
                        </div>
                        <div class="qualificationNameList">
                            @if($arrBeauticianQualification['success'])
                                @foreach($arrBeauticianQualification['data'] as $value)
                                    <div class="add-value">
                                        <i class="cross-icon delete-qualitification"></i>{{ucwords($value['qualification'])}}
                                        <input type="hidden" name="qualificationId" value="{{$value['id']}}" />
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="col s12 m6">
                        <div class="input-field">
                            <label class="active">Specialities</label>
                            <input id="speciality" type="text" name="speciality" class="speciality validate alphaonly" placeholder="Type here">
                        </div>
                        <div class="specialityNameList">
                            @if($arrBeauticianSpecialities['success'])
                                @foreach($arrBeauticianSpecialities['data'] as $value)
                                    <div class="add-value">
                                        <i class="cross-icon delete-speciality"></i>{{ucwords($value['speciality'])}}
                                        <input type="hidden" name="specialityId" value="{{$value['id']}}" />
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 padding-bottom-90">
                        <button class="bg-btn waves-effect set-btn sbmExpertise">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@stop
@section('scriptjs')
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/profile-expertise.js')}}"></script>
@stop

