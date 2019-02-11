@include('email.header')

Booking Confirmed
 </h5>

<p style="text-align: left;margin: 0;">Hi {{trim($customer->first_name.' '.$customer->last_name)}}, </p>

<div style="text-align: left;color: #676767;line-height: 19px;margin: 16px 0;">

		<p>We’re excited to confirm that you’ll get your <i>FIX</i> as per the details below: </p>

		<p><strong>Beauty Pro Name:</strong>{{trim($beautician->first_name.' '.$beautician->last_name)}}</p>
		<p>
		 <strong>Beauty Pro  Address:</strong>
		 <?php $addressArr = []; ?>
		 @if($beautician->address)
		   <?php array_push($addressArr, $beautician->address); ?>
		 @endif

		@if($beautician->suburb)
		   <?php array_push($addressArr, $beautician->suburb); ?>
		 @endif

		 @if($beautician->state)
		   <?php array_push($addressArr, $beautician->state); ?>
		 @endif

		 @if($beautician->country)
		   <?php array_push($addressArr, $beautician->country); ?>
		 @endif

		 @if($beautician->zipcode)
		   <?php array_push($addressArr, $beautician->zipcode); ?>
		 @endif

		 {{implode(", ",$addressArr)}}
		</p>
		<p><strong>Beauty Pro Mobile Number:</strong>{{$beautician->phone_number}}</p>

		<p><u><strong>Service Details:</strong></u></p>
		<div style="margin-left:20px;">
		   <?php $totalCost = 0; ?>
			@foreach($bookingArr as $booking)
			  <strong><i>Service Name:</i></strong> {{$booking['parentServiceName']}} -> {{$booking['serviceName']}}<br>
			  <strong><i>Service Date and Time:</i></strong> {{$booking['localStartDateTime']}}<br>
			  <strong><i>Service Duration:</i></strong> {{$booking['duration']}} mins<br>

			  @if($booking['hasMultipleSessions'])
			  	<strong><i>Session:</i></strong> {{$booking['sessionNo']}}<br>
			  @endif

			 @if($booking['discount'] || $booking['travelCost'])
			  	 <strong><i>Service Cost:</i></strong> ${{$booking['serviceCost']}}<br>
			  @endif

			  @if($booking['discount'])
			  	<strong><i>Discount:</i></strong> ${{round(($booking['serviceCost'] * ($booking['discount']/100)),2) }}<br>
			  @endif

			  @if($booking['travelCost'])
			  	<strong><i>Travel Cost:</i></strong> ${{$booking['travelCost']}}<br>
			  @endif

			  <strong><i>Cost:</i></strong> ${{$booking['actualCost'] + $booking['travelCost']}}<br>

			  @if($booking['bookingNote'])
			   <strong><i>Booking Note:</i></strong> {{$booking['bookingNote']}}
			  @endif

			  <p>

			   <?php $totalCost += ($booking['actualCost'] + $booking['travelCost']) ?>


			@endforeach

			@if(count($bookingArr) > 0)
				<p>
					<strong>Total Cost :</strong> ${{$totalCost}}
				</p>
			@endif
		</div>

		<p><strong>Service Address :</strong> {{$bookingAddress}}</p>



		<p>Your appointments are very important to us. They are reserved especially for you.  As a courtesy, you receive an email and an in-app notification from us reminding you of your booking. </p>

		<p>We understand that sometimes life gets in the way, but all we kindly ask is that should you need to cancel your booking, that you do so in app with a minimum of 24-hours’ notice wherever possible.  Failure to do so will result in a cancellation fee.</p>

		<p>Please refer to our terms and conditions (for Beauty Pro’s and Clients) available in-app for further information.</p>

		<p>
			Should you have any questions, or simply wish to provide feedback, we’d love to hear from you!  You can find us at <a href="mailto:help@beautyjunkie.com.au">help@beautyjunkie.com.au</a>
		</p>

		<p>Thanks!</p>

 </div>

<p style="text-align: left;margin: 0;">@include('email.signature')</p>

@include('email.footer')
