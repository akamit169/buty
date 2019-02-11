<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <!--Import Google Icon Font-->
    <title>Beauty Junkie</title>
    <!--Let browser know website is optimized for mobile-->
    <meta name="description" content="Beauty Junkie">
    <meta name="keywords" content="Beauty,makeup,hair,beauty services">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
</head>

<body style="background:#323232; color:#fff; margin:0; padding:0; font-size:14px;">
    <header style="background: #000; width: 100%; padding: 16px 15px; display: block; position: fixed; z-index: 999;">
        <div style="width: calc(100% - 200px); height: 20px; float: left; display: block; cursor: pointer;"><img src="{{URL::ASSET('/assets/beautician/images/logo.png')}}"></div>
    </header>
    <div style="padding-top: 52px; width: 500px; margin: 0 auto; display: block;">
        <div style="text-align: center; font-size: 21px; margin: 10px 0;">Report for the month of {{date('F')}}</div>
        <div style="margin: 10px 0;">
            <div style="margin:20px 0; font-size:18px;">Number of jobs per month</div>
            <div style="margin:10px 0;display: inline-block;width: 100%;">
                <label style="width:50%;display: block;float: left;">Total jobs completed : </label>
                <label style="width:50%;display: block;float: left;">
                    @if(empty($beauticianMonthlyReportDetail['total_completed_jobs']))
                        0
                    @else
                        {{$beauticianMonthlyReportDetail['total_completed_jobs']}}
                    @endif
                </label>
            </div>
            <div style="margin:10px 0;display: inline-block;width: 100%;">
                <label style="width:50%;display: block;float: left;">Total jobs cancelled : </label>
                <label style="width:50%;display: block;float: left;">
                    @if(empty($beauticianMonthlyReportDetail['total_cancelled_jobs']))
                        0
                    @else
                        {{$beauticianMonthlyReportDetail['total_cancelled_jobs']}}
                    @endif
                </label>
            </div>
            <div style="margin:10px 0;display: inline-block;width: 100%;">
                <label style="width:50%;display: block;float: left;">Total jobs disputed : </label>
                <label style="width:50%;display: block;float: left;">
                    @if(empty($beauticianMonthlyReportDetail['total_disputed_jobs']))
                        0
                    @else
                        {{$beauticianMonthlyReportDetail['total_disputed_jobs']}}
                    @endif
                </label>
            </div>
            <div style="margin: 20px 0; font-size:18px; display:inline-block;">Revenue generated per month</div>
            <div style="margin:10px 0;display: inline-block;width: 100%;">
                <label style="width:50%;display: block;float: left;">Total revenue : </label>
                <label style="width:50%;display: block;float: left;">
                    @if(empty($beauticianMonthlyReportDetail['total_cost']))
                        0
                    @else
                        $ {{$beauticianMonthlyReportDetail['total_cost']}}
                    @endif    
                </label>
            </div>
            </div>
            <div style="margin:20px 0; font-size:18px;">Report generated</div>
            <div style="margin:10px 0;display: inline-block;width: 100%;">
                <!--<img src="{{url('generate-rating-graph?id='.$beauticianMonthlyReportDetail->id)}}" style="width:100%; height:100%; display:block;"/>-->
                <img src="{{url('generate-complete-service-graph?id='.$beauticianMonthlyReportDetail['id'])}}" style="width:80%; height:80%; display:block;"/>
                <img src="{{url('generate-rating-graph?id='.$beauticianMonthlyReportDetail['id'])}}" style="width:100%; height:100%; display:block;"/>
            </div>
        </div>
    </div>
</body>

</html>