<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0 "); // Proxies.
?>

<!DOCTYPE html>
<html lang="en" class='app'>
<head>
    @include('admin.layout.header_include')
    <script>
         var SITE_URL = "{{url('')}}";
    </script>
</head>
<body class="" onunload="" >
    <!-- loading Html -->
    <div class="loading-overpay">
        <div class="loading">
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
        </div>
    </div>
    <!-- loading Html -->
    @yield('content')
    @yield('admin.layout.footer')
    @include('admin.layout.footer_include')
    @yield('scriptjs')
</body>
</html>