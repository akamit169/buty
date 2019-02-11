@extends('admin.layout.default_layout')
@section('title') {{{ 'Revenue Gain By Month' }}} @parent @stop {{-- Content --}}
@section('content')

<!--BEGIN PAGE WRAPPER-->
<section class="vbox">
    @include('admin.layout.menu_header')
    <section>
        <section class="hbox stretch">
            <!-- .aside --> 
            @include('admin.layout.sidebar')
            <!-- /.aside --> 
            <section id="content"> 
                <section class="vbox"> 
                     <!-- Page Heading -->
                    <header class="header bg-white b-b b-light"> 
                        <p><strong>Revenue Gain By Month</strong></p>
                        <ul class="breadcrumb pull-right mr-t-7"> 
                            <li><a href="{{url('admin/user')}}"><i class="icon icon-home"></i> Home</a></li> 
                            <li class="active">Revenue Gain By Month</li> 
                        </ul>
                    </header> 
                    <!-- End of Page Heading -->     
                            
                    <section class="scrollable wrapper w-f"> 
                        <div class="main-container padd-bottom-70">
                            @if (count($errors) > 0)
                                <!-- Form Error List -->
                                <div class="alert alert-danger">
                                    <strong>Whoops! Something went wrong!</strong>
                                    <br><br>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @elseif(Session::has('message') && !Session::has('status'))
                                <div class="alert alert-danger text-left">
                                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                    <ul><li>{{ Session::get('message') }}</li></ul>
                                </div>
                            @elseif(Session::has('message') && Session::has('status'))
                                <div class="alert alert-success text-left">
                                    <ul><li>{{ Session::get('message') }}</li></ul>
                                </div>
                            @endif  
                            <section class="">
                                <div class="table-responsive">
                                    <div>
                                        <select id="year">
                                            <option value="">Select Year</option>
                                            @foreach(range(2018, 2070) as $x)
                                            <option value="{{$x}}">{{$x}}</option>
                                            @endforeach
                                        </select>
                                        <button id="customSearch">Search</button>
                                    </div>
                                    <div id="donutchart" style="width: 900px; height: 500px;"></div>
                                </div>
                            </section>
                        </div>
                    </section>

                </section> 
            </section>
        </section>
    </section>
</section>
<!-- /#page-wrapper -->
<!--END PAGE WRAPPER-->
@section('scriptjs')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
        $('html').on('click', '#customSearch', function() {
            if($('#year').val() == '') {
                alert('Please select year.');
                return false;
            }
            drawChart();
        });
        function drawChart() {
            var year = $('#year').val();
            $.ajax({
                type: 'GET',
                url: SITE_URL + '/admin/revenue/revenue-gain-list-ajax?year='+year,
                success: function(response) {
                    var arrData = [];
                    $.each(response, function(index, value) {
                        if(index == 1) {
                            arrData.push(['Jan', {v: value, f: '$'+value}]);
                        } else if(index == 2) {
                            arrData.push(['Feb', {v: value, f: '$'+value}]);
                        } else if(index == 3) {
                            arrData.push(['Mar', {v: value, f: '$'+value}]);
                        } else if(index == 4) {
                            arrData.push(['Apr', {v: value, f: '$'+value}]);
                        } else if(index == 5) {
                            arrData.push(['May', {v: value, f: '$'+value}]);
                        } else if(index == 6) {
                            arrData.push(['June', {v: value, f: '$'+value}]);
                        } else if(index == 7) {
                            arrData.push(['July', {v: value, f: '$'+value}]);
                        } else if(index == 8) {
                            arrData.push(['Aug', {v: value, f: '$'+value}]);
                        } else if(index == 9) {
                            arrData.push(['Sep', {v: value, f: '$'+value}]);
                        } else if(index == 10) {
                            arrData.push(['Oct', {v: value, f: '$'+value}]);
                        } else if(index == 11) {
                            arrData.push(['Nov', {v: value, f: '$'+value}]);
                        } else if(index == 12) {
                            arrData.push(['Dec', {v: value, f: '$'+value}]);
                        }
                        
                    });
                    var data = new google.visualization.DataTable();
                    data.addColumn('string', 'Revenue');
                    data.addColumn('number', 'Current');

                    data.addRows(arrData);

                    var options = {
                      title: 'Revenue Gain'
                    };

                    var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
                    chart.draw(data, options);
                },
                error: function() {

                }
            });
        }
    </script>

@stop
@stop
