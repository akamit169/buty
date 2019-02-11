<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>@yield('title')</title>
    <meta name="_token" content="{!! csrf_token() !!}"/>
    @include('beautician.main-layout.header-include')
    @yield('extendcss')   
</head>

<body>
    <div class="wrapper">
        @yield('content')

      @if(!isset($userType))
        <footer>
                <div class="footer-container">
                    <a href="{{url('beautician/terms-and-conditions')}}" class="tc-div">Terms &amp; Conditions</a>
                    <a href="{{url('beautician/privacy-policy')}}" class="privacy-div">Privacy Policy</a>
                </div>
        </footer>
      @endif
    </div>
    @include('beautician.main-layout.footer-include')
    @yield('scriptjs')
</body>
</html>