@include('email.header')

Boooking Confirmed
 </h5>

<p style="text-align: left;margin: 0;">Hi {{trim($beautician->first_name.' '.$beautician->last_name)}}, </p>


<div style="text-align: left;color: #676767;line-height: 19px;margin: 16px 0;">

	<p>We’re excited to inform you that a booking has been made for your business as per the details below:</p>

	<p><strong>Client Name:</strong>{{trim($customer->first_name.' '.$customer->last_name)}}</p>
	<p>
	 <strong>Client Address:</strong>
	  <?php $addressArr = []; ?>
	 @if($beautician->address)
	   <?php array_push($addressArr, $customer->address); ?>
	 @endif

	@if($customer->suburb)
	   <?php array_push($addressArr, $customer->suburb); ?>
	 @endif

	 @if($beautician->state)
	   <?php array_push($addressArr, $beautician->state); ?>
	 @endif

	 @if($customer->country)
	   <?php array_push($addressArr, $customer->country); ?>
	 @endif

	 @if($customer->zipcode)
	   <?php array_push($addressArr, $customer->zipcode); ?>
	 @endif

	 {{implode(", ",$addressArr)}}

	</p>
	<p><strong>Client Mobile Number:</strong>{{$customer->phone_number}}</p>

	@if($customer->allergies)
	  <p><strong>Client Allergies :</strong> {{$customer->allergies}}</p>
	@endif

	<p><u><strong>Service Details:</strong></u></p>
	<div style="margin-left:20px;">
	    <?php $totalCost = 0; ?>
		@foreach($bookingArr as $booking)
		 <p>
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


	<p>We know that your clients are important to you, but we also understand that life can sometimes get in the way.  Although you will not be charged a cancellation fee for cancelling a booking in-app, we strongly recommend that you practice great customer service, and personally contact your client to notify them of any cancellation. It shows you care.</p>

	<p>Please refer to our Terms and Conditions available in app for further information (i.e. our Cancellation Policy for Beauty Pro’s and Clients).</p>

	<p>Should you have any questions, or simply wish to provide feedback, we’d love to hear from you!  You can reach us at <a href="mailto:help@beautyjunkie.com.au">help@beautyjunkie.com.au</a></p>

	<p>Thanks!</p>

</div>

<p style="text-align: left;margin: 0;">@include('email.signature')</p>

@include('email.footer')

