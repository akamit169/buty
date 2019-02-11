@include('email.header',['showTick' => 'false'])

Booking Cancelled
 </h5>

<p style="text-align: left;margin: 0;"> Hi {{$user}}, </p>

<div style="text-align: left;color: #676767;line-height: 19px;margin: 16px 0;">
	<p>Regrettably, your booking (as per below) has been <strong>cancelled:</strong> </p>

	<div style="margin-left:20px;">
		
		  <strong><i>Beauty Pro Name:</i></strong> {{trim($booking['beautician_name'])}}<br>
		  <strong><i>Client Name:</i></strong> {{trim($booking['customer_name'])}}<br>
		  <strong><i>Service Name:</i></strong> {{$booking['parent_service_name']}} -> {{$booking['service_name']}}<br>
		  <strong><i>Service Date and Time:</i></strong> {{$bookingDateTime}}<br>
		  <strong><i>Service Duration:</i></strong> {{$booking['duration']}} mins<br>
		  <strong>Service Address :</strong> {{$booking['booking_address']}}<br>

		  @if($booking['has_multiple_sessions'])
		  	<strong><i>Session:</i></strong> {{$booking['session_no']}}<br>
		  @endif

		  <strong><i>Cost:</i></strong> ${{$booking['actual_cost'] + $booking['travel_cost']}} 
		</p>
	</div>

	<p>You can make another appointment <i>pronto</i> to get your Fix <i>anytime, anywhere</i> via the Beauty Junkie app!</p>

	<p>Please reach out to us at <a href="mailto:help@beautyjunkie.com.au">help@beautyjunkie.com.au</a> if you have any questions or feedback.  We’d love to hear from you!</p>

	<p>Thanks!</p>

</div>

<p style="text-align: left;margin: 0;">@include('email.signature')</p>

@include('email.footer')




