<meta charset="utf-8" />
<title>beautyjunkie :: @yield('title')</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<meta name="_token" content="{!! csrf_token() !!}"/>

<link rel="icon" type="image/png" href="{{URL::asset('assets/admin/images/favicon.ico')}}" />
<link rel="manifest" href="manifest.json">
<link rel="mask-icon" href="safari-pinned-tab.svg" color="#5bbad5">
<meta name="theme-color" content="#ffffff">
<link rel="stylesheet" href="{{URL::asset('assets/admin/css/font.css')}}" type="text/css" />
<link href='https://fonts.googleapis.com/css?family=Open+Sans:600' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="{{URL::asset('assets/admin/css/app.v1.css')}}" type="text/css" />
<link rel="stylesheet" href="{{URL::asset('assets/admin/css/cropper.css')}}" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{URL::asset('assets/admin/css/jquery.dataTables.css')}}">
<link rel="stylesheet" type="text/css" href="{{URL::asset('assets/admin/css/dataTables.responsive.css')}}">
<link rel="stylesheet" href="{{URL::asset('assets/admin/css/custom.css')}}" type="text/css" />
<link rel="stylesheet" href="{{URL::asset('assets/admin/css/dev.css')}}" type="text/css" />


<link rel="stylesheet" href="{{URL::asset('assets/admin/css/datepicker.css')}}" type="text/css" />

<link rel="stylesheet" href="{{URL::asset('assets/admin/css/prettify_skins_tomorrow.css')}}" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="{{URL::asset('assets/admin/js/app.js')}}"></script>
@yield('admin.layout.header_script')

