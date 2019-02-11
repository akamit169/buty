<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>@yield('title')</title>
    <meta name="_token" content="{!! csrf_token() !!}"/>
    @include('beautician.main-layout.header-include')
    @yield('extendcss')
    <script>
        var SITE_URL = "{{url('')}}";
        var IS_USER_LOGGEDIN = {{!empty(\Auth::check())?1:0}};
        var DEFAULT_IMAGE="{{env('S3_BUCKET_PATH').env('DEFAULT_USER_NEW_IMAGE')}}";
    </script>
    <script type="text/javascript">
        function checkLoginStatus(data){
                   
                    if(data=='401'){
                        setTimeout(function(){ 
                            document.location.href = SITE_URL+'/beautician/login'; 
                        }, 500);

                    } else if(data=='403') {
                        setTimeout(function(){ 
                            document.location.href = SITE_URL+'/beautician/login'; 
                        }, 500);
                    }
                    return;
            }
        function toTitleCase(str)
        {
            return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
        }    
    </script>    
</head>

<body>
    <div class="loader"><i></i></div>
    <div class="wrapper">
       
       @if(!isset($api))
        @include('beautician.main-layout.header')
       @endif

        @yield('content')
        <footer>
                <div class="footer-container">
                    @if(isset($api))
                     <a href="{{url('api/beautician/terms-and-conditions')}}" class="tc-div{{\Request::is('api/beautician/terms-and-conditions')?' selected':''}}">Beauty Pro Terms &amp; Conditions</a>
                     <a href="{{url('api/beautician/privacy-policy')}}" class="privacy-div{{\Request::is('api/beautician/privacy-policy')?' selected':''}}">Beauty Pro Privacy Policy</a>

                     <a href="{{url('api/customer/terms-and-conditions')}}" class="tc-div{{\Request::is('api/customer/terms-and-conditions')?' selected':''}}">Client Terms &amp; Conditions</a>
                     <a href="{{url('api/customer/privacy-policy')}}" class="privacy-div{{\Request::is('api/customer/privacy-policy')?' selected':''}}">Client Privacy Policy</a>

                    @else
                       <a href="{{url('beautician/terms-and-conditions')}}" class="tc-div{{\Request::is('beautician/terms-and-conditions')?' selected':''}}">Beauty Pro Terms &amp; Conditions</a>
                       <a href="{{url('beautician/privacy-policy')}}" class="privacy-div{{\Request::is('beautician/privacy-policy')?' selected':''}}">Beauty Pro Privacy Policy</a>

                       <a href="{{url('customer/terms-and-conditions')}}" class="tc-div{{\Request::is('customer/terms-and-conditions')?' selected':''}}">Client Terms &amp; Conditions</a>
                       <a href="{{url('customer/privacy-policy')}}" class="privacy-div{{\Request::is('customer/privacy-policy')?' selected':''}}">Client Privacy Policy</a>

                    @endif
                </div>
        </footer>
    </div>
    @include('beautician.main-layout.footer-include')
    @yield('scriptjs')
</body>
</html>