@extends('beautician.main-layout.main-layout')
@section('title') {{'Profile'}}@stop
@section('content')

       <div class="inner-container">
        <div class="inner-main-heading">
            Upload Work
        </div>

        @if(Session::get('error_msg')) 
            <div class="alert alert-danger alert-dismissable server-error success-msg-div">
                {{Session::get('error_msg')}}
            </div>
        @endif
        
        @if (isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissable server-error success-msg-div">
                <h4><i class="icon fa fa-ban"></i> Error !</h4> 
                @foreach($errors->all() as $key=>$message)
                <label class="error-msg">* {{$message}}</label>
                <br/> 
                @endforeach
            </div> 
        @endif

        <div class="upload-work-wrap">
         <form  action="saveBeauticianPortfolio"  method="post" enctype="multipart/form-data">
           {!! csrf_field() !!}
          <div class="after-select-image">
            <div class="row">
              <div class="col s12 m12">
                Upload an image that you want the other beauty junkies to see.
                <div class="uploaded-image">
                    <span class="delete-caption icon icon-delete"></span>
                </div>
              </div>
            </div>
            <div class="work-service">
              <div class="row">
                <div class="col s12 m6">
                  <div class="lage-label">
                      Select Service category
                  </div>
                </div>
                <div class="col s12 m6">
                  <div class="form-fields">
                      <div class="drop-down">
                          <div class="input-field">
                              <select id="services" name=serviceId>
                               @foreach($services as $service)
                                <option value="{{$service->id}}">{{$service->name}}</option>
                               @endforeach
                              </select>
                          </div>
                      </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col s12 m12 padding-bottom-90">
                  <button type="submit" class="bg-btn waves-effect">Upload Image</button>
                </div>
              </div>
            </div>
          </div>
          <div class="upload-btn">
              <a class="btn-up-work" href="javascript:;">
                  Browse files
                  <input id="profile-img" type="file" class="file" name="portfolioPic" accept="image/*" />
              </a>
          </div>
          </form>
        </div>

    </div>
  

@stop

@section('scriptjs')
 <script type="text/javascript" src="{{URL::asset('assets/beautician/js/lightgallery.js')}}"></script>
 <script type="text/javascript" src="{{URL::asset('assets/beautician/js/upload-work.js')}}"></script>
@stop