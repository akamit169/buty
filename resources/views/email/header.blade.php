<div class="email-wrap" style="max-width: 600px;margin: 0 auto;background-color: #5D5D5A;background-image: url({{asset('assets/email/images/bg.png')}});background-repeat: repeat-x;text-align: center;font-family: -apple-system, BlinkMacSystemFont, sans-serif;padding-bottom: 32px;">
		<div class="top" style="height: 186px;background-color: #000;">
			<img src="{{asset('assets/email/images/header-logo.png')}}" alt="" style="margin-top: 26px;max-width: 224px;" />
		</div>

		<div class="pop-up" style="background-color: #fff;border-radius: 4px;max-width: 374px;margin: 0 auto;-webkit-box-shadow: 0 24px 24px 0 rgba(0, 0, 0, 0.24), 0 12px 12px 0 rgba(0, 0, 0, 0.12);box-shadow: 0 24px 24px 0 rgba(0, 0, 0, 0.24), 0 12px 12px 0 rgba(0, 0, 0, 0.12);font-size: 12px;padding: 42px 38px;">
			
			@if(!isset($showTick))
			<img src="{{asset('assets/email/images/tick.jpg')}}" alt="" class="tick" style="max-width: 76px;">
			@endif

			<h5 style="font-size: 16px;font-weight: normal;margin: 40px 0 30px;"> 

			